<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('permissions.index', compact('permissions'));
    }

    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'display_name' => ['required', 'string', 'max:255'],
            'group' => ['required', 'string', 'max:255'],
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    public function syncFromRoutes()
    {
        $permissionsFound = [];

        foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
            foreach ($route->gatherMiddleware() as $middleware) {
                if (\Illuminate\Support\Str::startsWith($middleware, 'permission:')) {
                    $permissionName = \Illuminate\Support\Str::after($middleware, 'permission:');
                    if (!empty($permissionName)) {
                        $permissionsFound[] = $permissionName;
                    }
                }
            }
        }

        $uniquePermissions = array_unique($permissionsFound);
        $addedCount = 0;

        foreach ($uniquePermissions as $permName) {
            $parts = explode('.', $permName);
            $group = ucfirst($parts[0] ?? 'General');
            $displayName = ucwords(str_replace(['-', '.'], ' ', $permName));

            // FirstOrCreate inserts if it doesn't exist, and ignores if it already exists
            $permission = Permission::firstOrCreate(
                ['name' => $permName],
                [
                    'display_name' => $displayName,
                    'group' => $group,
                ]
            );

            if ($permission->wasRecentlyCreated) {
                $addedCount++;
            }
        }

        return redirect()->route('permissions.index')
            ->with('success', "Permission registry successfully updated! Added {$addedCount} new permissions.");
    }
}
