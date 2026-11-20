<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InquiryConfirmationMail;
use App\Models\Inquiry;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

        $phone = trim($validated['phone']);
        $email = !empty($validated['email']) ? strtolower(trim($validated['email'])) : null;

        $forwardedIp = $request->header('X-Client-IP')
            ?: explode(',', (string) $request->header('X-Forwarded-For'))[0];
        $forwardedIp = trim((string) $forwardedIp);
        $ipAddress = filter_var($forwardedIp, FILTER_VALIDATE_IP) ? $forwardedIp : $request->ip();

        $alreadySubmittedToday = Inquiry::query()
            ->whereDate('created_at', today())
            ->where(function ($q) use ($phone, $email, $ipAddress) {
                $q->where('phone', $phone);

                if ($email) {
                    $q->orWhereRaw('LOWER(email) = ?', [$email]);
                }

                if ($ipAddress) {
                    $q->orWhere('ip_address', $ipAddress);
                }
            })
            ->exists();

        if ($alreadySubmittedToday) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an inquiry today. Please try again tomorrow.',
            ], 429);
        }

        try {
            DB::beginTransaction();

            $inquiry = Inquiry::create([
                'name' => $validated['name'],
                'phone' => $phone,
                'email' => $email,
                'service_type' => $validated['service_type'],
                'address' => $validated['address'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'ip_address' => $ipAddress,
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
