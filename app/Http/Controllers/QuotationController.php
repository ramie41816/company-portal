<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Quotation::query();

        if (!auth()->user()->isModuleAdmin('quotation')) {
            $query->where('created_by', auth()->id());
        }

        $quotations = $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('quoted_to', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('reference_project', 'like', "%{$search}%");
            });
        })
            ->when($dateFrom, fn($q, $date) => $q->whereDate('quotation_date', '>=', $date))
            ->when($dateTo, fn($q, $date) => $q->whereDate('quotation_date', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quotations.index', compact('quotations', 'search', 'dateFrom', 'dateTo'));
    }

    public function dashboard()
    {
        $query = Quotation::query();

        if (!auth()->user()->isModuleAdmin('quotation')) {
            $query->where('created_by', auth()->id());
        }

        $totalQuotations = (clone $query)->count();
        $totalValue = (clone $query)->sum('total');
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $pendingCount = (clone $query)->whereIn('status', ['pending', 'revision', 'draft'])->count();
        $ongoingCount = (clone $query)->where('status', 'ongoing')->count();
        $recentQuotations = (clone $query)->with('items')->latest()->take(5)->get();

        return view('quotations.dashboard', compact(
            'totalQuotations',
            'totalValue',
            'approvedCount',
            'pendingCount',
            'ongoingCount',
            'recentQuotations'
        ));
    }

    public function create()
    {
        return view('quotations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quoted_to' => ['required', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_address' => ['nullable', 'string', 'max:500'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'attention' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'reference_project' => ['nullable', 'string', 'max:255'],
            'other_info' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.is_sub' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated) {
            $year = date('Y', strtotime($validated['quotation_date']));
            $latestId = Quotation::max('id') + 1;
            $quotationNumber = 'QT-' . $year . '-' . str_pad($latestId, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'reference_project' => $validated['reference_project'] ?? null,
                'prepared_by' => $validated['prepared_by'] ?? null,
                'other_info' => $validated['other_info'] ?? null,
                'quoted_to' => $validated['quoted_to'],
                'client_company' => $validated['client_company'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'status' => 'draft',
                'remarks' => null,
                'status_modified_by' => auth()->user()->name ?? 'System',
                'status_modified_at' => now(),
                'created_by' => auth()->id(),
                'total' => 0,
            ]);

            $grandTotal = 0;
            $lastMainItemId = null;

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['unit_price'] * $item['quantity'];
                $grandTotal += $lineTotal;

                $isSub = !empty($item['is_sub']) && $item['is_sub'] == '1';

                $createdItem = QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'parent_id' => $isSub ? $lastMainItemId : null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);

                if (!$isSub) {
                    $lastMainItemId = $createdItem->id;
                }
            }

            $quotation->update(['total' => $grandTotal]);
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }]);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->load('items');
        return view('quotations.edit', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'quoted_to' => ['required', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_address' => ['nullable', 'string', 'max:500'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'attention' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quotation_date'],
            'reference_project' => ['nullable', 'string', 'max:255'],
            'other_info' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:draft,pending,approved,rejected,emailed,ongoing,completed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.is_sub' => ['nullable'],
        ]);

        DB::transaction(function () use ($validated, $quotation) {
            $status_modified_by = auth()->user()->userInfo?->initials . " (" . auth()->user()->id . ")";

            $quotation->update([
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'reference_project' => $validated['reference_project'] ?? null,
                'prepared_by' => $validated['prepared_by'] ?? null,
                'other_info' => $validated['other_info'] ?? null,
                'quoted_to' => $validated['quoted_to'],
                'client_company' => $validated['client_company'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => $validated['status'],
                'status_modified_by' => trim($status_modified_by) ?? 'System',
                'status_modified_at' => now(),
            ]);

            $quotation->items()->delete();

            $grandTotal = 0;
            $lastMainItemId = null;

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['unit_price'] * $item['quantity'];
                $grandTotal += $lineTotal;

                $isSub = !empty($item['is_sub']) && $item['is_sub'] == '1';

                $createdItem = QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'parent_id' => $isSub ? $lastMainItemId : null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);

                if (!$isSub) {
                    $lastMainItemId = $createdItem->id;
                }
            }

            $quotation->update(['total' => $grandTotal]);
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation archived successfully.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Quotation::onlyTrashed();

        if (!auth()->user()->isModuleAdmin('quotation')) {
            $query->where('created_by', auth()->id());
        }

        $quotations = $query->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('quoted_to', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('reference_project', 'like', "%{$search}%");
            });
        })
            ->when($dateFrom, fn($q, $date) => $q->whereDate('quotation_date', '>=', $date))
            ->when($dateTo, fn($q, $date) => $q->whereDate('quotation_date', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quotations.archives', compact('quotations', 'search', 'dateFrom', 'dateTo'));
    }

    public function restore($id)
    {
        $quotation = Quotation::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->restore();

        return redirect()->route('quotations.archives')->with('success', 'Quotation restored successfully.');
    }

    public function forceDelete($id)
    {
        $quotation = Quotation::onlyTrashed()->findOrFail($id);

        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->items()->delete();
        $quotation->forceDelete();

        return redirect()->route('quotations.archives')->with('success', 'Quotation permanently deleted.');
    }

    // --- Prepare Quotation Module Methods ---
    public function prepareQuotation(Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $quotation->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }]);

        return view('quotations.prepare-quotation', compact('quotation'));
    }

    public function generateQuotationPdf(Request $request, Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $quotation->load('items');

        $signatureDataUri = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureDataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $pdf = Pdf::loadView('templates.pdf.quotation-template', compact('quotation', 'signatureDataUri'));
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);

        return $pdf->stream('Quotation-' . $quotation->quotation_number . '.pdf');
    }

    public function sendQuotationEmail(Request $request, Quotation $quotation)
    {
        if (!auth()->user()->isModuleAdmin('quotation') && $quotation->created_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'recipient_email' => ['required', 'email', 'max:255'],
            'other_info' => ['nullable', 'string', 'max:1000'],
            'signature_file' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $quotation->load('items');
        $recipientEmail = $validated['recipient_email'];

        if ($request->filled('other_info')) {
            $quotation->other_info = $request->input('other_info');
        }

        $signatureDataUri = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $signatureDataUri = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        try {
            Mail::to($recipientEmail)->send(new QuotationMail($quotation, $signatureDataUri));

            $quotation->update(['status' => 'emailed']);

            return back()->with('success', 'Quotation successfully emailed to ' . $recipientEmail);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
