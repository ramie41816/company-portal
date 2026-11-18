<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of active services.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $services = Service::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal-management.services.index', compact('services', 'search'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('portal-management.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'icon_class'        => ['required', 'string', 'max:100'],
            'short_description' => ['required', 'string', 'max:500'],
            'full_description'  => ['required', 'string'],
            'features'          => ['nullable', 'string'],
            'images.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'sort_order'        => ['required', 'integer', 'min:1'],
            'is_active'         => ['required', 'boolean'],
        ]);

        // Process feature bullet points (convert lines into array)
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $validated['features']))));
        }

        // Process uploaded images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('services', 'public');
                $imagePaths[] = $path;
            }
        }

        Service::create([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']),
            'icon_class'        => $validated['icon_class'],
            'short_description' => $validated['short_description'],
            'full_description'  => $validated['full_description'],
            'features'          => $featuresArray,
            'images'            => $imagePaths,
            'sort_order'        => $validated['sort_order'],
            'is_active'         => $validated['is_active'],
        ]);

        return redirect()->route('portal.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service details.
     */
    public function show(Service $service)
    {
        return view('portal-management.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('portal-management.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'icon_class'        => ['required', 'string', 'max:100'],
            'short_description' => ['required', 'string', 'max:500'],
            'full_description'  => ['required', 'string'],
            'features'          => ['nullable', 'string'],
            'images.*'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'sort_order'        => ['required', 'integer', 'min:1'],
            'is_active'         => ['required', 'boolean'],
        ]);

        // Process feature bullet points
        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_values(array_filter(array_map('trim', explode("\n", $validated['features']))));
        }

        // Retain existing photos and append new ones
        $existingImages = is_array($service->images) ? $service->images : [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('services', 'public');
                $existingImages[] = $path;
            }
        }

        $service->update([
            'title'             => $validated['title'],
            'slug'              => Str::slug($validated['title']),
            'icon_class'        => $validated['icon_class'],
            'short_description' => $validated['short_description'],
            'full_description'  => $validated['full_description'],
            'features'          => $featuresArray,
            'images'            => $existingImages,
            'sort_order'        => $validated['sort_order'],
            'is_active'         => $validated['is_active'],
        ]);

        return redirect()->route('portal.services.index')->with('success', 'Service updated successfully.');
    }

    /**
     * Soft-delete (Archive) the specified service.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('portal.services.index')->with('success', 'Service archived successfully.');
    }

    /**
     * Display a listing of archived services.
     */
    public function archives(Request $request)
    {
        $search = $request->input('search');

        $services = Service::onlyTrashed()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('portal-management.services.archives', compact('services', 'search'));
    }

    /**
     * Restore the soft-deleted service.
     */
    public function restore($id)
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();

        return redirect()->route('portal.services.archives')->with('success', 'Service restored successfully.');
    }

    /**
     * Permanently delete the specified service from storage.
     */
    public function forceDelete($id)
    {
        DB::transaction(function () use ($id) {
            $service = Service::onlyTrashed()->findOrFail($id);

            // Clean up stored image assets from storage disk
            if (is_array($service->images)) {
                foreach ($service->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $service->forceDelete();
        });

        return redirect()->route('portal.services.archives')->with('success', 'Service permanently deleted.');
    }

    /**
     * Remove an individual photo via AJAX.
     */
    public function destroyImage(Service $service, Request $request)
    {
        $imagePath = $request->input('image_path');
        $currentImages = is_array($service->images) ? $service->images : [];

        if (($key = array_search($imagePath, $currentImages)) !== false) {
            unset($currentImages[$key]);
            Storage::disk('public')->delete($imagePath);

            $service->update(['images' => array_values($currentImages)]);

            return response()->json([
                'success' => true,
                'message' => 'Photo removed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Photo not found.'
        ], 404);
    }
}
