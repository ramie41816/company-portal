<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleItem;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $schedules = Schedule::query()
            ->when($search, function ($query, $search) {
                $query->where('schedule_number', 'like', "%{$search}%")
                    ->orWhere('quoted_to', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('reference_project', 'like', "%{$search}%");
            })
            ->when($dateFrom, fn($q, $date) => $q->whereDate('schedule_date', '>=', $date))
            ->when($dateTo, fn($q, $date) => $q->whereDate('schedule_date', '<=', $date))
            ->latest('schedule_date')
            ->paginate(10)
            ->withQueryString();

        return view('schedules.index', compact('schedules', 'search', 'dateFrom', 'dateTo'));
    }

    public function dashboard()
    {
        $totalSchedules = Schedule::count();
        $totalValue = Schedule::sum('total');
        $ongoingCount = Schedule::where('status', 'ongoing')->count();
        $completedCount = Schedule::where('status', 'completed')->count();
        $recentSchedules = Schedule::latest()->take(5)->get();

        // Fetch active schedules
        $calendarSchedules = Schedule::select('id', 'schedule_number', 'quoted_to', 'schedule_date', 'status', 'total')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->schedule_number . ' - ' . $schedule->quoted_to,
                    'date' => $schedule->schedule_date ? $schedule->schedule_date->format('Y-m-d') : null,
                    'url' => route('schedules.show', $schedule->id),
                    'status' => $schedule->status,
                ];
            });

        return view('schedules.dashboard', compact(
            'totalSchedules',
            'totalValue',
            'ongoingCount',
            'completedCount',
            'recentSchedules',
            'calendarSchedules'
        ));
    }

    public function create()
    {
        return view('schedules.create', [
            'action' => route('schedules.store')
        ]);
    }

    // Handles conversion from an Approved Quotation
    public function createFromQuotation(Quotation $quotation)
    {
        $quotation->load('items');

        $schedule = new Schedule([
            'quotation_id'       => $quotation->id,
            'schedule_number'    => 'SCH-' . str_replace('QTE-', '', $quotation->quotation_number),
            'schedule_date'      => now(),
            'reference_project'  => $quotation->reference_project,
            'prepared_by'        => $quotation->prepared_by,
            'other_info'         => $quotation->other_info,
            'quoted_to'          => $quotation->quoted_to,
            'client_company'     => $quotation->client_company,
            'client_address'     => $quotation->client_address,
            'client_phone'       => $quotation->client_phone,
            'attention'          => $quotation->attention,
            'contact_email'      => $quotation->contact_email,
            'total'              => $quotation->total,
            'status'             => 'scheduled',
        ]);

        $savedItems = $quotation->items->map(fn($item) => [
            'id'          => null,
            'parent_id'   => $item->parent_id,
            'description' => $item->description,
            'unit_price'  => (float) $item->unit_price,
            'quantity'    => (float) $item->quantity,
        ])->all();

        if (empty($savedItems)) {
            $savedItems = [['id' => null, 'parent_id' => null, 'description' => '', 'unit_price' => '', 'quantity' => 1]];
        }

        return view('schedules.create', [
            'schedule'   => $schedule,
            'quotation'  => $quotation,
            'savedItems' => $savedItems,
            'action'     => route('schedules.store')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quotation_id'      => 'nullable|exists:quotations,id',
            'quoted_to'         => 'required|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'client_address'    => 'nullable|string',
            'client_phone'      => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:255',
            'attention'         => 'nullable|string|max:255',
            'prepared_by'       => 'nullable|string|max:255',
            'schedule_date'     => 'required|date',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'reference_project' => 'nullable|string|max:255',
            'status'            => 'required|string',
            'remarks'           => 'nullable|string',
            'other_info'        => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.is_sub'      => 'nullable|boolean',
        ]);

        $validated['schedule_number'] = 'SCH-' . strtoupper(uniqid());
        $validated['created_by'] = auth()->user()->name ?? 'System';

        $schedule = Schedule::create($validated);

        $lastMainItem = null;
        $grandTotal = 0;

        foreach ($request->items as $itemData) {
            $isSub = isset($itemData['is_sub']) && $itemData['is_sub'] == '1';
            $unitPrice = (float) $itemData['unit_price'];
            $quantity = (float) $itemData['quantity'];
            $total = $unitPrice * $quantity;
            $grandTotal += $total;

            $scheduleItem = ScheduleItem::create([
                'schedule_id' => $schedule->id,
                'parent_id'   => $isSub ? optional($lastMainItem)->id : null,
                'description' => $itemData['description'],
                'unit_price'  => $unitPrice,
                'quantity'    => $quantity,
                'total'       => $total,
            ]);

            if (!$isSub) {
                $lastMainItem = $scheduleItem;
            }
        }

        $schedule->update(['total' => $grandTotal]);

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule successfully created.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load('items.subItems', 'quotation');
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        $schedule->load('items');
        return view('schedules.edit', [
            'schedule' => $schedule,
            'action' => route('schedules.update', $schedule)
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'quoted_to'         => 'required|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'client_address'    => 'nullable|string',
            'client_phone'      => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:255',
            'attention'         => 'nullable|string|max:255',
            'prepared_by'       => 'nullable|string|max:255',
            'schedule_date'     => 'required|date',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'reference_project' => 'nullable|string|max:255',
            'status'            => 'required|string',
            'remarks'           => 'nullable|string',
            'other_info'        => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.is_sub'      => 'nullable|boolean',
        ]);

        $schedule->update($validated);

        // Reset items
        $schedule->items()->delete();

        $lastMainItem = null;
        $grandTotal = 0;

        foreach ($request->items as $itemData) {
            $isSub = isset($itemData['is_sub']) && $itemData['is_sub'] == '1';
            $unitPrice = (float) $itemData['unit_price'];
            $quantity = (float) $itemData['quantity'];
            $total = $unitPrice * $quantity;
            $grandTotal += $total;

            $scheduleItem = ScheduleItem::create([
                'schedule_id' => $schedule->id,
                'parent_id'   => $isSub ? optional($lastMainItem)->id : null,
                'description' => $itemData['description'],
                'unit_price'  => $unitPrice,
                'quantity'    => $quantity,
                'total'       => $total,
            ]);

            if (!$isSub) {
                $lastMainItem = $scheduleItem;
            }
        }

        $schedule->update(['total' => $grandTotal]);

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule successfully updated.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule moved to archives.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $schedules = Schedule::onlyTrashed()
            ->when($search, fn($q, $s) => $q->where('schedule_number', 'like', "%{$s}%")->orWhere('quoted_to', 'like', "%{$s}%"))
            ->latest()
            ->paginate(10);

        return view('schedules.archives', compact('schedules', 'search'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);
        $schedule->restore();
        return redirect()->route('schedules.archives')->with('success', 'Schedule restored successfully.');
    }

    public function forceDelete($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);
        $schedule->items()->delete();
        $schedule->forceDelete();
        return redirect()->route('schedules.archives')->with('success', 'Schedule permanently deleted.');
    }
}
