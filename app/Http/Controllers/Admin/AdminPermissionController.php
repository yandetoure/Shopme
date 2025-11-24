<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class AdminPermissionController extends Controller
{
    /**
     * Afficher la liste des permissions
     */
    public function index()
    {
        $permissions = Permission::withCount('roles')->orderBy('name')->get()
            ->groupBy(function($permission) {
                return explode(' ', $permission->name)[0] ?? 'other';
            });
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.permissions.create', compact('roles'));
    }

    /**
     * Sauvegarder une nouvelle permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        // Assigner aux rôles
        if ($request->filled('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            foreach ($roles as $role) {
                $role->givePermissionTo($permission);
            }
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission créée avec succès !');
    }

    /**
     * Afficher une permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Permission $permission)
    {
        $roles = Role::all();
        $permission->load('roles');
        return view('admin.permissions.edit', compact('permission', 'roles'));
    }

    /**
     * Mettre à jour une permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $permission->update(['name' => $validated['name']]);

        // Synchroniser les rôles
        if ($request->filled('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $permission->syncRoles($roles);
        } else {
            $permission->roles()->detach();
        }

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission mise à jour avec succès !');
    }

    /**
     * Supprimer une permission
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission supprimée avec succès !');
    }
}




