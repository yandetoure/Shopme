<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    /**
     * Afficher la liste des rôles
     */
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode(' ', $permission->name)[0] ?? 'other';
        });
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Sauvegarder un nouveau rôle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        // Assigner les permissions
        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle créé avec succès !');
    }

    /**
     * Afficher un rôle
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode(' ', $permission->name)[0] ?? 'other';
        });
        $role->load('permissions');
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Mettre à jour un rôle
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        // Synchroniser les permissions
        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle mis à jour avec succès !');
    }

    /**
     * Supprimer un rôle
     */
    public function destroy(Role $role)
    {
        // Empêcher la suppression des rôles par défaut
        $defaultRoles = ['super_admin', 'admin', 'vendeur', 'client'];
        if (in_array($role->name, $defaultRoles)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Impossible de supprimer un rôle système.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rôle supprimé avec succès !');
    }
}






