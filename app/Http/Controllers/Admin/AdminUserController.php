<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filtres
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Sauvegarder un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $user = User::create($validated);

        // Assigner les rôles
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->assignRole($roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Afficher un utilisateur
     */
    public function show(User $user)
    {
        $user->load('roles', 'permissions', 'orders', 'cartItems');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        // Mettre à jour les rôles
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression du super admin
        if ($user->hasRole('super_admin') && User::role('super_admin')->count() === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer le dernier super admin.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès !');
    }

    /**
     * Toggle l'état actif/inactif d'un utilisateur
     */
    public function toggleStatus(User $user)
    {
        // Empêcher la désactivation du dernier super admin
        if ($user->hasRole('super_admin') && $user->is_active && \Spatie\Permission\Models\Role::where('name', 'super_admin')->first()->users()->where('is_active', true)->count() === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de désactiver le dernier super admin actif.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activé' : 'désactivé';
        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$status} avec succès !");
    }
}

