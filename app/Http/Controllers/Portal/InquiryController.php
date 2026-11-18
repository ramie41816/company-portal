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

        $inquiries = Inquiry::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('service_type', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal-management.inquiries.index', compact('inquiries', 'search', 'status'));
    }

    /**
     * Display the specified inquiry details.
     */
    public function show(Inquiry $inquiry)
    {
        $inquiry->load('assignedStaff');

        return view('portal-management.inquiries.show', compact('inquiry'));
    }

    /**
     * Update the status of the inquiry.
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,scheduled,completed,cancelled'],
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

        $inquiries = Inquiry::onlyTrashed()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal-management.inquiries.archives', compact('inquiries', 'search'));
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
