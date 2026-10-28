<?php

namespace App\Http\Controllers;

use App\Mail\QuotationMail;
use App\Models\Quotation;
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

        $query = Quotation::latest();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('quoted_to', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('reference_project', 'like', "%{$search}%")
                    ->orWhere('attention', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('quotation_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('quotation_date', '<=', $dateTo);
        }

        $quotations = $query->paginate(15)->withQueryString();

        return view('quotations.index', compact('quotations', 'search', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
        return view('quotations.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $request) {
            $quotation = Quotation::create([
                'quotation_date' => $data['quotation_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'reference_project' => $data['reference_project'] ?? null,
                'prepared_by' => $data['prepared_by'] ?? null,
                'quoted_to' => $data['quoted_to'],
                'client_company' => $data['client_company'] ?? null,
                'client_address' => $data['client_address'] ?? null,
                'client_phone' => $data['client_phone'] ?? null,
                'attention' => $data['attention'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $total = $this->saveItems($quotation, $data['items']);

            $quotation->update([
                'quotation_number' => sprintf('QT-%s-%04d', now()->format('Y'), $quotation->id),
                'total' => $total,
            ]);
        });

        // Redirect to the show page
        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation created successfully.');
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
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $quotation) {
            $quotation->update([
                'quotation_date' => $data['quotation_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'reference_project' => $data['reference_project'] ?? null,
                'prepared_by' => $data['prepared_by'] ?? null,
                'quoted_to' => $data['quoted_to'],
                'client_company' => $data['client_company'] ?? null,
                'client_address' => $data['client_address'] ?? null,
                'client_phone' => $data['client_phone'] ?? null,
                'attention' => $data['attention'] ?? null,
                'contact_email' => $data['contact_email'] ?? null,
            ]);

            // Replace existing items with the updated ones
            $quotation->items()->delete();
            $total = $this->saveItems($quotation, $data['items']);

            $quotation->update(['total' => $total]);
        });

        // Redirect to show page
        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully.');
    }

    /**
     * Soft delete: hides the quotation from the list but keeps the record
     * (and its items) in the database. The quotation number is never reused.
     */
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('quotations.archives')
            ->with('success', 'Quotation '.$quotation->quotation_number.' deleted successfully.');
    }

    public function archives(Request $request)
    {
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Quotation::onlyTrashed()->latest();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhere('quoted_to', 'like', "%{$search}%")
                    ->orWhere('client_company', 'like', "%{$search}%")
                    ->orWhere('reference_project', 'like', "%{$search}%")
                    ->orWhere('attention', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFrom)) {
            $query->whereDate('quotation_date', '>=', $dateFrom);
        }

        if (! empty($dateTo)) {
            $query->whereDate('quotation_date', '<=', $dateTo);
        }

        $quotations = $query->paginate(15)->withQueryString();

        return view('quotations.archives', compact('quotations', 'search', 'dateFrom', 'dateTo'));
    }

    public function restore($id)
    {
        $quotation = Quotation::withTrashed()->findOrFail($id);
        $quotation->restore();

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation '.$quotation->quotation_number.' restored successfully.');
    }

    public function forceDelete($id)
    {
        $quotation = Quotation::withTrashed()->findOrFail($id);
        $quotation->forceDelete();

        return redirect()->route('quotations.archives')
            ->with('success', 'Quotation '.$quotation->quotation_number.' permanently deleted.');
    }

    /**
     * Validation rules supporting all template fields.
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'quoted_to' => ['required', 'string', 'max:255'],
            'client_company' => ['nullable', 'string', 'max:255'],
            'client_address' => ['nullable', 'string', 'max:500'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'attention' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date'],
            'reference_project' => ['nullable', 'string', 'max:255'],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);
    }

    /**
     * Saves line items and calculates total.
     */
    private function saveItems(Quotation $quotation, array $items): float
    {
        $grandTotal = 0;

        foreach ($items as $item) {
            $lineTotal = round($item['unit_price'] * $item['quantity'], 2);

            $quotation->items()->create([
                'description' => $item['description'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'total' => $lineTotal,
            ]);

            $grandTotal += $lineTotal;
        }

        return $grandTotal;
    }

    /**
     * Generate and stream the PDF quotation.
     */
    public function generatePDF(Quotation $quotation)
    {
        $quotation->load('items');

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation'));

        return $pdf->stream('Quotation-'.$quotation->quotation_number.'.pdf');
    }

    /**
     * Send an email to the client.
     */
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

            return back()->with('success', 'Quotation successfully emailed to '.$quotation->contact_email);
        } catch (\Exception $e) {
            // Clean up even if it fails
            if ($signaturePath) {
                Storage::disk('public')->delete($signaturePath);
            }

            return back()->with('error', 'Failed to send email: '.$e->getMessage());
        }
    }
}
