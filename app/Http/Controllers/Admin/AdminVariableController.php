<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeType;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminVariableController extends Controller
{
    /**
     * Afficher la liste des types d'attributs
     */
    public function index()
    {
        $attributeTypes = AttributeType::withCount('allValues')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.variables.index', compact('attributeTypes'));
    }

    /**
     * Afficher le formulaire de création d'un type d'attribut
     */
    public function create()
    {
        return view('admin.variables.create');
    }

    /**
     * Sauvegarder un nouveau type d'attribut
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attribute_types,name',
            'type' => 'required|in:text,color,image,select',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Vérifier l'unicité du slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (AttributeType::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['is_active'] = $request->has('is_active');

        AttributeType::create($validated);

        return redirect()->route('admin.variables.index')
            ->with('success', 'Type d\'attribut créé avec succès !');
    }

    /**
     * Afficher un type d'attribut avec ses valeurs
     */
    public function show(AttributeType $variable)
    {
        $variable->load('allValues');
        return view('admin.variables.show', compact('variable'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(AttributeType $variable)
    {
        $variable->load('allValues');
        return view('admin.variables.edit', compact('variable'));
    }

    /**
     * Mettre à jour un type d'attribut
     */
    public function update(Request $request, AttributeType $variable)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attribute_types,name,' . $variable->id,
            'type' => 'required|in:text,color,image,select',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Générer le slug si le nom a changé
        if ($variable->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (AttributeType::where('slug', $validated['slug'])->where('id', '!=', $variable->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $validated['is_active'] = $request->has('is_active');

        $variable->update($validated);

        return redirect()->route('admin.variables.index')
            ->with('success', 'Type d\'attribut mis à jour avec succès !');
    }

    /**
     * Supprimer un type d'attribut
     */
    public function destroy(AttributeType $variable)
    {
        $variable->delete();

        return redirect()->route('admin.variables.index')
            ->with('success', 'Type d\'attribut supprimé avec succès !');
    }

    /**
     * Ajouter une valeur à un type d'attribut
     */
    public function storeValue(Request $request, AttributeType $variable)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:attribute_values,value,NULL,id,attribute_type_id,' . $variable->id,
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('attribute-values', 'public');
        }

        $validated['attribute_type_id'] = $variable->id;
        $validated['is_active'] = $request->has('is_active');

        AttributeValue::create($validated);

        return redirect()->route('admin.variables.show', $variable)
            ->with('success', 'Valeur ajoutée avec succès !');
    }

    /**
     * Mettre à jour une valeur d'attribut
     */
    public function updateValue(Request $request, AttributeType $variable, AttributeValue $value)
    {
        // Vérifier que la valeur appartient bien au type d'attribut
        if ($value->attribute_type_id !== $variable->id) {
            abort(403, 'Cette valeur n\'appartient pas à ce type d\'attribut.');
        }

        $validated = $request->validate([
            'value' => 'required|string|max:255|unique:attribute_values,value,' . $value->id . ',id,attribute_type_id,' . $variable->id,
            'color_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($value->image) {
                Storage::disk('public')->delete($value->image);
            }
            $validated['image'] = $request->file('image')->store('attribute-values', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $value->update($validated);

        return redirect()->route('admin.variables.show', $variable)
            ->with('success', 'Valeur mise à jour avec succès !');
    }

    /**
     * Supprimer une valeur d'attribut
     */
    public function destroyValue(AttributeType $variable, AttributeValue $value)
    {
        // Vérifier que la valeur appartient bien au type d'attribut
        if ($value->attribute_type_id !== $variable->id) {
            abort(403, 'Cette valeur n\'appartient pas à ce type d\'attribut.');
        }

        if ($value->image) {
            Storage::disk('public')->delete($value->image);
        }

        $value->delete();

        return redirect()->route('admin.variables.show', $variable)
            ->with('success', 'Valeur supprimée avec succès !');
    }
}

