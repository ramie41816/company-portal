<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $quotations = Quotation::query()
            ->when($search, function ($query, $search) {
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
        $totalQuotations = Quotation::count();
        $totalValue = Quotation::sum('total');
        $approvedCount = Quotation::where('status', 'approved')->count();
        $pendingCount = Quotation::whereIn('status', ['pending', 'revision', 'draft'])->count();
        $ongoingCount = Quotation::where('status', 'ongoing')->count();
        $recentQuotations = Quotation::with('items')->latest()->take(5)->get();

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

            // Sequential parsing: Assigns sub-items to the most recent main item automatically
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
        $quotation->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }]);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        return view('quotations.edit', compact('quotation'));
    }

    public function update(Request $request, Quotation $quotation)
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
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation archived successfully.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $quotations = Quotation::onlyTrashed()
            ->when($search, function ($query, $search) {
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
        $quotation->restore();

        return redirect()->route('quotations.archives')->with('success', 'Quotation restored successfully.');
    }

    public function forceDelete($id)
    {
        $quotation = Quotation::onlyTrashed()->findOrFail($id);
        $quotation->items()->delete();
        $quotation->forceDelete();

        return redirect()->route('quotations.archives')->with('success', 'Quotation permanently deleted.');
    }

    public function sendEmail(Request $request, Quotation $quotation)
    {
        if (empty($quotation->contact_email)) {
            return back()->with('error', 'This client does not have a valid email address on file.');
        }

        $signaturePath = null;

        if ($request->hasFile('email_signature')) {
            $signaturePath = $request->file('email_signature')->store('temp_signatures', 'public');
        }

        try {
            Mail::to($quotation->contact_email)->send(new QuotationMail($quotation, $signaturePath));

            if ($signaturePath) {
                Storage::disk('public')->delete($signaturePath);
            }

            $quotation->update(['status' => 'emailed']);

            return back()->with('success', 'Quotation successfully emailed to ' . $quotation->contact_email);
        } catch (\Exception $e) {
            if ($signaturePath) {
                Storage::disk('public')->delete($signaturePath);
            }

            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function generatePDF(Quotation $quotation)
    {
        $quotation->load('items');
        $pdf = Pdf::loadView('pdf.quotation-template', compact('quotation'));
        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        return $pdf->stream('Quotation-' . $quotation->quotation_number . '.pdf');
    }

    // Show the CoC setup screen with quotation items
    public function createCoc(Quotation $quotation)
    {
        $quotation->load(['items' => function ($query) {
            $query->orderBy('parent_id')->orderBy('id');
        }]);

        return view('quotations.coc-setup', compact('quotation'));
    }

    public function generateCocPdf(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'general_remarks' => ['nullable', 'string', 'max:1000'],
            'executed_by' => ['nullable', 'string', 'max:255'],
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ]);

        $quotation->load('items');
        $submittedItems = $request->input('items', []);

        $processedItems = [];
        foreach ($quotation->items as $item) {
            $itemData = $submittedItems[$item->id] ?? [];
            $isIncluded = isset($itemData['included']) && $itemData['included'] == '1';

            $processedItems[] = [
                'item' => $item,
                'included' => $isIncluded,
                'remarks' => $itemData['remarks'] ?? null,
            ];
        }

        $generalRemarks = $request->input('general_remarks');
        $executedBy = trim($request->input('executed_by')) ?: ($quotation->prepared_by ?? config('company.name'));

        // Handle uploaded eSignature image
        $tempSignaturePath = null;
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $filename = 'sig_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            // Store temporarily in public storage
            $path = $file->storeAs('temp-signatures', $filename, 'public');
            $tempSignaturePath = storage_path('app/public/' . $path);
        }

        $pdf = Pdf::loadView('pdf.coc-template', compact(
            'quotation',
            'processedItems',
            'generalRemarks',
            'executedBy',
            'tempSignaturePath'
        ));

        $pdf->getDomPDF()->set_option('isPhpEnabled', true);

        return $pdf->stream('Certificate-of-Completion-' . $quotation->quotation_number . '.pdf');
    }
}
