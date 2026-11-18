<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Inquiry;

class DashboardController extends Controller
{
    public function index()
    {
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();
        $inactiveServices = Service::where('is_active', false)->count();
        $archivedServices = Service::onlyTrashed()->count();

        $totalInquiries = Inquiry::count();
        $pendingInquiries = Inquiry::where('status', 'pending')->count();
        $recentInquiries = Inquiry::latest()->take(5)->get();

        return view('portal-management.dashboard', compact(
            'totalServices',
            'activeServices',
            'inactiveServices',
            'archivedServices',
            'totalInquiries',
            'pendingInquiries',
            'recentInquiries'
        ));
    }
}
