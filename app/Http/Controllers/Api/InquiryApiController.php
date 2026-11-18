<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Mail\InquiryConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class InquiryApiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'service_type' => 'required|string',
            'address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $inquiry = Inquiry::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'service_type' => $validated['service_type'],
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);

            DB::commit();

            if (!empty($inquiry->email)) {
                Mail::to($inquiry->email)->send(new InquiryConfirmationMail($inquiry));
            }

            return response()->json([
                'success' => true,
                'message' => 'Inquiry created successfully.',
                'data' => $inquiry,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create inquiry. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
