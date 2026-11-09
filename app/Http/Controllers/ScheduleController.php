<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleItem;
use App\Models\SchedulePersonnel;
use App\Models\ScheduleExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateOfCompletionMail;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Schedule::query();

        if (!auth()->user()->isModuleAdmin('schedule')) {
            $query->where('created_by', auth()->id());
        }

        $schedules = $query->when($search, function ($query, $search) {
            $query->where('schedule_number', 'like', "%{$search}%")
                ->orWhere('scheduled_to', 'like', "%{$search}%")
                ->orWhere('client_company', 'like', "%{$search}%")
                ->orWhere('reference_project', 'like', "%{$search}%");
        })
            ->when($dateFrom, fn($q, $date) => $q->whereDate('schedule_date', '>=', $date))
            ->when($dateTo, fn($q, $date) => $q->whereDate('schedule_date', '<=', $date))
            ->orderBy('schedule_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('schedules.index', compact('schedules', 'search', 'dateFrom', 'dateTo'));
    }

    public function dashboard()
    {
        $query = Schedule::query();

        if (!auth()->user()->isModuleAdmin('schedule')) {
            $query->where('created_by', auth()->id());
        }

        $totalSchedules = (clone $query)->count();
        $totalValue = (clone $query)->sum('total');
        $ongoingCount = (clone $query)->where('status', 'ongoing')->count();
        $completedCount = (clone $query)->where('status', 'completed')->count();

        $recentSchedules = (clone $query)->orderBy('schedule_date', 'desc')->take(5)->get();

        $calendarSchedules = (clone $query)->select('id', 'schedule_number', 'scheduled_to', 'schedule_date', 'status', 'total')
            ->orderBy('schedule_date', 'asc')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'quote_number' => $schedule->schedule_number,
                    'scheduled_to' => $schedule->scheduled_to,
                    'date' => $schedule->schedule_date ? $schedule->schedule_date->format('Y-m-d') : null,
                    'time' => $schedule->schedule_date ? $schedule->schedule_date->format('h:i A') : null,
                    'start' => $schedule->schedule_date ? $schedule->schedule_date->format('Y-m-d\TH:i:s') : null,
                    'title' => $schedule->scheduled_to . ' (' . $schedule->schedule_number . ')',
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

    public function createFromQuotation(Quotation $quotation)
    {
        $existingSchedule = Schedule::where('quotation_id', $quotation->id)->first();

        if ($existingSchedule) {
            return redirect()->route('schedules.edit', $existingSchedule)
                ->with('info', 'A schedule already exists for this quotation. You are now editing it.');
        }

        $quotation->load('items');

        $schedule = new Schedule([
            'quotation_id'       => $quotation->id,
            'schedule_number'    => 'SCH-' . str_replace('QTE-', '', $quotation->quotation_number),
            'schedule_date'      => now(),
            'reference_project'  => $quotation->reference_project,
            'prepared_by'        => $quotation->prepared_by,
            'other_info'         => $quotation->other_info,
            'scheduled_to'       => $quotation->quoted_to,
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
            'scheduled_to'      => 'required|string|max:255',
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
            'personnel'             => 'nullable|array',
            'personnel.*.name'      => 'required|string|max:255',
            'personnel.*.payment'   => 'required|numeric|min:0',
            'personnel.*.is_paid'   => 'nullable|boolean',
            'personnel.*.role_in_schedule' => 'nullable|string|max:255',
            'personnel.*.notes'     => 'nullable|string',
            'expenses'              => 'nullable|array',
            'expenses.*.expense_type' => 'required|string|max:255',
            'expenses.*.amount'     => 'required|numeric|min:0',
            'expenses.*.notes'      => 'nullable|string',
        ]);

        $validated['schedule_number'] = 'SCH-' . strtoupper(uniqid());
        $validated['created_by'] = auth()->id();

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

        if ($request->has('personnel')) {
            foreach ($request->personnel as $personnelData) {
                SchedulePersonnel::create([
                    'schedule_id'      => $schedule->id,
                    'name'             => $personnelData['name'],
                    'payment'          => (float) $personnelData['payment'],
                    'is_paid'          => isset($personnelData['is_paid']) && $personnelData['is_paid'] == '1',
                    'role_in_schedule' => $personnelData['role_in_schedule'] ?? null,
                    'notes'            => $personnelData['notes'] ?? null,
                ]);
            }
        }

        if ($request->has('expenses')) {
            foreach ($request->expenses as $expenseData) {
                ScheduleExpense::create([
                    'schedule_id'  => $schedule->id,
                    'expense_type' => $expenseData['expense_type'],
                    'amount'       => (float) $expenseData['amount'],
                    'notes'        => $expenseData['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule successfully created.');
    }

    public function show(Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->load('items.subItems', 'quotation', 'personnel.user', 'expenses');
        return view('schedules.show', compact('schedule'));
    }

    public function edit(Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->load('items', 'personnel', 'expenses');
        return view('schedules.edit', [
            'schedule' => $schedule,
            'action' => route('schedules.update', $schedule)
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'scheduled_to'      => 'required|string|max:255',
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
            'personnel'             => 'nullable|array',
            'personnel.*.name'      => 'required|string|max:255',
            'personnel.*.payment'   => 'required|numeric|min:0',
            'personnel.*.is_paid'   => 'nullable|boolean',
            'personnel.*.role_in_schedule' => 'nullable|string|max:255',
            'personnel.*.notes'     => 'nullable|string',
            'expenses'              => 'nullable|array',
            'expenses.*.expense_type' => 'required|string|max:255',
            'expenses.*.amount'     => 'required|numeric|min:0',
            'expenses.*.notes'      => 'nullable|string',
        ]);

        if ($schedule->quotation_id) {
            Schedule::where('quotation_id', $schedule->quotation_id)
                ->where('id', '!=', $schedule->id)
                ->delete();
        }

        $schedule->update($validated);

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

        $schedule->personnel()->delete();

        if ($request->has('personnel')) {
            foreach ($request->personnel as $personnelData) {
                SchedulePersonnel::create([
                    'schedule_id'      => $schedule->id,
                    'name'             => $personnelData['name'],
                    'payment'          => (float) $personnelData['payment'],
                    'is_paid'          => isset($personnelData['is_paid']) && $personnelData['is_paid'] == '1',
                    'role_in_schedule' => $personnelData['role_in_schedule'] ?? null,
                    'notes'            => $personnelData['notes'] ?? null,
                ]);
            }
        }

        $schedule->expenses()->delete();

        if ($request->has('expenses')) {
            foreach ($request->expenses as $expenseData) {
                ScheduleExpense::create([
                    'schedule_id'  => $schedule->id,
                    'expense_type' => $expenseData['expense_type'],
                    'amount'       => (float) $expenseData['amount'],
                    'notes'        => $expenseData['notes'] ?? null,
                ]);
            }
        }

        return redirect()->route('schedules.show', $schedule)
            ->with('success', 'Schedule successfully updated.');
    }

    public function destroy(Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule moved to archives.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $query = Schedule::onlyTrashed();

        if (!auth()->user()->isModuleAdmin('schedule')) {
            $query->where('created_by', auth()->id());
        }

        $schedules = $query->when($search, fn($q, $s) => $q->where('schedule_number', 'like', "%{$s}%")->orWhere('scheduled_to', 'like', "%{$s}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('schedules.archives', compact('schedules', 'search'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->restore();
        return redirect()->route('schedules.archives')->with('success', 'Schedule restored successfully.');
    }

    public function forceDelete($id)
    {
        $schedule = Schedule::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->items()->delete();
        $schedule->personnel()->delete();
        $schedule->expenses()->delete();
        $schedule->forceDelete();
        return redirect()->route('schedules.archives')->with('success', 'Schedule permanently deleted.');
    }

    public function prepareCoc(Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }]);

        return view('schedules.prepare-coc', compact('schedule'));
    }

    public function generateCocPdf(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = \Validator::make($request->all(), [
            'items' => ['nullable', 'array'],
            'general_remarks' => ['nullable', 'string', 'max:1000'],
            'executed_by' => ['nullable', 'string', 'max:255'],
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $schedule->load('items');
        $submittedItems = $request->input('items', []);

        $processedItems = [];
        foreach ($schedule->items as $item) {
            $itemData = $submittedItems[$item->id] ?? [];
            $isIncluded = isset($itemData['included']) && $itemData['included'] == '1';

            $processedItems[] = [
                'item' => $item,
                'included' => $isIncluded,
                'remarks' => $itemData['remarks'] ?? null,
            ];
        }

        $generalRemarks = $request->input('general_remarks');
        $executedBy = trim($request->input('executed_by')) ?: ($schedule->prepared_by ?? config('company.name'));

        $signatureDataUri = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureDataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $pdf = Pdf::loadView('templates.pdf.coc-template', compact(
            'schedule',
            'processedItems',
            'generalRemarks',
            'executedBy',
            'signatureDataUri'
        ));

        $pdf->getDomPDF()->set_option('isPhpEnabled', true);

        return $pdf->stream('Certificate-of-Completion-' . $schedule->schedule_number . '.pdf');
    }

    public function sendCocEmail(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
            'items' => ['nullable', 'array'],
            'general_remarks' => ['nullable', 'string', 'max:1000'],
            'executed_by' => ['nullable', 'string', 'max:255'],
            'signature_file' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $schedule->load('items');
        $submittedItems = $request->input('items', []);

        $processedItems = [];
        foreach ($schedule->items as $item) {
            $itemData = $submittedItems[$item->id] ?? [];
            $isIncluded = isset($itemData['included']) && $itemData['included'] == '1';

            $processedItems[] = [
                'item' => $item,
                'included' => $isIncluded,
                'remarks' => $itemData['remarks'] ?? null,
            ];
        }

        $generalRemarks = $request->input('general_remarks');
        $executedBy = trim($request->input('executed_by')) ?: ($schedule->prepared_by ?? config('company.name'));
        $recipientEmail = $validated['recipient_email'];

        $signatureDataUri = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureDataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        try {
            Mail::to($recipientEmail)->send(new CertificateOfCompletionMail(
                $schedule,
                $processedItems,
                $generalRemarks,
                $executedBy,
                $signatureDataUri
            ));

            return back()->with('success', 'Certificate of Completion successfully emailed to ' . $recipientEmail);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function prepareReport(Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $schedule->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }, 'personnel', 'expenses', 'quotation']);

        return view('schedules.prepare-report', compact('schedule'));
    }

    public function generateReportPdf(Request $request, Schedule $schedule)
    {
        if (!auth()->user()->isModuleAdmin('schedule') && $schedule->created_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = \Validator::make($request->all(), [
            'executed_by' => ['nullable', 'string', 'max:255'],
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $schedule->load(['items', 'personnel', 'expenses', 'quotation']);

        $executedBy = trim($request->input('executed_by')) ?: ($schedule->prepared_by ?? config('company.name'));

        $signatureDataUri = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureDataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $pdf = Pdf::loadView('templates.pdf.schedule-report-template', compact(
            'schedule',
            'executedBy',
            'signatureDataUri'
        ));

        $pdf->getDomPDF()->set_option('isPhpEnabled', true);

        return $pdf->stream('Schedule-Report-' . $schedule->schedule_number . '.pdf');
    }
}
