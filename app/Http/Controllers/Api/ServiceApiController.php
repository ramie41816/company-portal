<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;

class ServiceApiController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($service) {
                // Ensure every image path is converted into a full public URL
                $images = collect($service->images ?? [])->map(function ($path) {
                    return str_starts_with($path, 'http') ? $path : asset(Storage::url($path));
                });

                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'slug' => $service->slug,
                    'icon_class' => $service->icon_class ?? 'bi-tools',
                    'short_description' => $service->short_description,
                    'full_description' => $service->full_description,
                    'features' => $service->features ?? [],
                    'images' => $images,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }
}
