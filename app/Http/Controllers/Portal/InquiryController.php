<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryController extends Controller
{
    /**
     * Display a listing of client inquiries.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $serviceType = $request->input('service_type');

        $serviceTypes = Inquiry::query()
            ->whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        $inquiries = Inquiry::query()
            ->when($search, function ($query, $search) {
                $escaped = addcslashes($search, '%_\\');
                $query->where(function ($q) use ($escaped) {
                    $q->where('reference_id', 'like', "%{$escaped}%")
                        ->orWhere('name', 'like', "%{$escaped}%")
                        ->orWhere('email', 'like', "%{$escaped}%")
                        ->orWhere('phone', 'like', "%{$escaped}%")
                        ->orWhere('address', 'like', "%{$escaped}%")
                        ->orWhere('service_type', 'like', "%{$escaped}%");
                });
            })
            ->when($status, fn($q, $status) => $q->where('status', $status))
            ->when($serviceType, fn($q, $serviceType) => $q->where('service_type', $serviceType))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal-management.inquiries.index', compact('inquiries', 'search', 'status', 'serviceType', 'serviceTypes'));
    }

    /**
     * Display the specified inquiry details.
     */
    public function show(Inquiry $inquiry)
    {
        return view('portal-management.inquiries.show', compact('inquiry'));
    }

    /**
     * Update the status of the inquiry.
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,facilitated,completed,cancelled'],
        ]);

        $inquiry->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }

    /**
     * Soft-delete (Archive) the specified inquiry.
     */
    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('portal.inquiries.index')->with('success', 'Inquiry archived successfully.');
    }

    /**
     * Display a listing of archived inquiries.
     */
    public function archives(Request $request)
    {
        $search = $request->input('search');
        $serviceType = $request->input('service_type');

        $serviceTypes = Inquiry::onlyTrashed()
            ->whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        $inquiries = Inquiry::onlyTrashed()
            ->when($search, function ($query, $search) {
                $escaped = addcslashes($search, '%_\\');
                $query->where(function ($q) use ($escaped) {
                    $q->where('name', 'like', "%{$escaped}%")
                        ->orWhere('email', 'like', "%{$escaped}%")
                        ->orWhere('phone', 'like', "%{$escaped}%")
                        ->orWhere('address', 'like', "%{$escaped}%")
                        ->orWhere('service_type', 'like', "%{$escaped}%");
                });
            })
            ->when($serviceType, fn($q, $serviceType) => $q->where('service_type', $serviceType))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'portal-management.inquiries.archives',
            compact('inquiries', 'search', 'serviceType', 'serviceTypes')
        );
    }

    /**
     * Restore the soft-deleted inquiry.
     */
    public function restore($id)
    {
        $inquiry = Inquiry::onlyTrashed()->findOrFail($id);
        $inquiry->restore();

        return redirect()->route('portal.inquiries.archives')->with('success', 'Inquiry restored successfully.');
    }

    /**
     * Permanently delete the specified inquiry from storage.
     */
    public function forceDelete($id)
    {
        DB::transaction(function () use ($id) {
            $inquiry = Inquiry::onlyTrashed()->findOrFail($id);
            $inquiry->forceDelete();
        });

        return redirect()->route('portal.inquiries.archives')->with('success', 'Inquiry permanently deleted.');
    }
}
