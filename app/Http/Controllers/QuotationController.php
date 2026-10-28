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
            ->when($dateFrom, fn ($q, $date) => $q->whereDate('quotation_date', '>=', $date))
            ->when($dateTo, fn ($q, $date) => $q->whereDate('quotation_date', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quotations.index', compact('quotations', 'search', 'dateFrom', 'dateTo'));
    }

    public function dashboard()
    {
        // Gather summary statistics
        $totalQuotations = Quotation::count();
        $totalValue = Quotation::sum('total');
        $approvedCount = Quotation::where('status', 'approved')->count();
        $pendingCount = Quotation::whereIn('status', ['pending', 'revision', 'draft'])->count();
        $ongoingCount = Quotation::where('status', 'ongoing')->count();

        // Fetch the 5 most recent quotations
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($validated) {
            $year = date('Y', strtotime($validated['quotation_date']));
            $latestId = Quotation::max('id') + 1;
            $quotationNumber = 'QT-'.$year.'-'.str_pad($latestId, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'reference_project' => $validated['reference_project'] ?? null,
                'prepared_by' => $validated['prepared_by'] ?? null,
                'quoted_to' => $validated['quoted_to'],
                'client_company' => $validated['client_company'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'status' => 'draft', // Defaults to draft on creation[cite: 1]
                'remarks' => null,
                'status_modified_by' => auth()->user()->name ?? 'System',
                'status_modified_at' => now(),
                'created_by' => auth()->id(),
                'total' => 0,
            ]);

            $grandTotal = 0;

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['unit_price'] * $item['quantity'];
                $grandTotal += $lineTotal;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            $quotation->update(['total' => $grandTotal]);
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('items');

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
            'remarks' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:draft,pending,approved,rejected,emailed,ongoing,completed'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($validated, $quotation) {
            $updateData = [
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'] ?? null,
                'reference_project' => $validated['reference_project'] ?? null,
                'prepared_by' => $validated['prepared_by'] ?? null,
                'quoted_to' => $validated['quoted_to'],
                'client_company' => $validated['client_company'] ?? null,
                'client_address' => $validated['client_address'] ?? null,
                'client_phone' => $validated['client_phone'] ?? null,
                'attention' => $validated['attention'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'status' => $validated['status'],
                'status_modified_by' => auth()->user()->name ?? 'System',
                'status_modified_at' => now(),
            ];

            $quotation->update($updateData);

            // Re-sync items
            $quotation->items()->delete();

            $grandTotal = 0;

            foreach ($validated['items'] as $item) {
                $lineTotal = $item['unit_price'] * $item['quantity'];
                $grandTotal += $lineTotal;

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
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
            ->when($dateFrom, fn ($q, $date) => $q->whereDate('quotation_date', '>=', $date))
            ->when($dateTo, fn ($q, $date) => $q->whereDate('quotation_date', '<=', $date))
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

        // Capture temporary signature uploaded via the email modal prompt
        if ($request->hasFile('email_signature')) {
            $signaturePath = $request->file('email_signature')->store('temp_signatures', 'public');
        }

        try {
            Mail::to($quotation->contact_email)->send(new QuotationMail($quotation, $signaturePath));

            // Clean up temporary signature file from storage
            if ($signaturePath) {
                Storage::disk('public')->delete($signaturePath);
            }

            // Update status of quotation to emailed
            $quotation->update(['status' => 'emailed']);

            return back()->with('success', 'Quotation successfully emailed to '.$quotation->contact_email);
        } catch (\Exception $e) {
            // Clean up even if it fails
            if ($signaturePath) {
                Storage::disk('public')->delete($signaturePath);
            }

            return back()->with('error', 'Failed to send email: '.$e->getMessage());
        }
    }

    public function generatePDF(Quotation $quotation)
    {
        $quotation->load('items');

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));

        return $pdf->stream('Quotation-'.$quotation->quotation_number.'.pdf');
    }
}
