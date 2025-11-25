<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\AttributeType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    /**
     * Afficher la liste des produits
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'categories']);

        // Filtres
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(100);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $attributeTypes = AttributeType::where('is_active', true)
            ->with('values')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Préparer les données pour JavaScript
        $attributeTypesJson = $attributeTypes->map(function($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'type' => $type->type,
                'values' => $type->values->map(function($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'color_code' => $value->color_code,
                        'image' => $value->image ? url('storage/' . $value->image) : null,
                    ];
                })->values()
            ];
        })->values();
        
        return view('admin.products.create', compact('categories', 'attributeTypes', 'attributeTypesJson'));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'category_id' => 'nullable|exists:categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_on_sale' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'sale_unit' => 'required|in:unit,dozen',
            'image' => 'nullable|image|max:2048',
            'secondary_images' => 'nullable|array',
            'secondary_images.*' => 'image|max:2048',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'is_discovery' => 'boolean',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_type_id' => 'required|exists:attribute_types,id',
            'attributes.*.values' => 'required|array|min:1',
            'attributes.*.values.*' => 'exists:attribute_values,id',
        ]);

        // Générer le slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Upload de l'image principale
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Upload des images secondaires
        $secondaryImages = [];
        if ($request->hasFile('secondary_images')) {
            foreach ($request->file('secondary_images') as $file) {
                $secondaryImages[] = $file->store('products', 'public');
            }
        }
        if (!empty($secondaryImages)) {
            $validated['images'] = $secondaryImages;
        }

        $validated['slug'] = $slug;
        $validated['in_stock'] = $validated['stock_quantity'] > 0;
        $validated['sale_unit'] = $validated['sale_unit'] ?? 'unit';
        $validated['is_discovery'] = $request->boolean('is_discovery');

        $product = Product::create($validated);

        // Attacher les catégories
        if ($request->filled('categories')) {
            $product->categories()->sync($request->categories);
        } elseif ($request->filled('category_id')) {
            $product->categories()->attach($request->category_id);
        }

        // Gérer les attributs
        if ($request->filled('attributes')) {
            $this->saveProductAttributes($product, $request->input('attributes', []));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit créé avec succès !');
    }

    /**
     * Afficher un produit
     */
    public function show(Product $product)
    {
        $product->load(['category', 'categories', 'productAttributes.values', 'variations']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'productAttributes.values']);
        $categories = Category::where('is_active', true)->get();
        $attributeTypes = AttributeType::where('is_active', true)
            ->with('values')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        // Préparer les données pour JavaScript
        $attributeTypesJson = $attributeTypes->map(function($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'type' => $type->type,
                'slug' => $type->slug,
                'values' => $type->values->map(function($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'color_code' => $value->color_code,
                        'image' => $value->image ? url('storage/' . $value->image) : null,
                    ];
                })->values()
            ];
        })->values();
        
        // Préparer les attributs existants pour le JavaScript
        $existingAttributes = [];
        foreach ($product->productAttributes as $productAttribute) {
            $attributeType = $attributeTypes->firstWhere('slug', $productAttribute->slug);
            if ($attributeType) {
                $selectedValues = $productAttribute->values->pluck('value')->toArray();
                $selectedValueIds = $attributeType->values->whereIn('value', $selectedValues)->pluck('id')->toArray();
                
                $existingAttributes[] = [
                    'attribute_type_id' => $attributeType->id,
                    'selected_values' => $selectedValueIds,
                ];
            }
        }
        
        return view('admin.products.edit', compact('product', 'categories', 'attributeTypes', 'attributeTypesJson', 'existingAttributes'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_on_sale' => 'boolean',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'supplier_name' => 'nullable|string|max:255',
            'sale_unit' => 'required|in:unit,dozen',
            'image' => 'nullable|image|max:2048',
            'secondary_images' => 'nullable|array',
            'secondary_images.*' => 'image|max:2048',
            'existing_images' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'is_discovery' => 'boolean',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_type_id' => 'required|exists:attribute_types,id',
            'attributes.*.values' => 'required|array|min:1',
            'attributes.*.values.*' => 'exists:attribute_values,id',
        ]);

        // Générer le slug si le nom a changé
        if ($product->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Upload de la nouvelle image principale
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Gérer les images secondaires
        $existingImages = $request->input('existing_images', []);
        $currentImages = $product->images ?? [];
        
        // Supprimer les images qui ne sont plus dans existing_images
        $imagesToDelete = array_diff($currentImages, $existingImages);
        foreach ($imagesToDelete as $imageToDelete) {
            Storage::disk('public')->delete($imageToDelete);
        }
        
        // Ajouter les nouvelles images secondaires
        $newSecondaryImages = [];
        if ($request->hasFile('secondary_images')) {
            foreach ($request->file('secondary_images') as $file) {
                $newSecondaryImages[] = $file->store('products', 'public');
            }
        }
        
        // Combiner les images existantes (qui n'ont pas été supprimées) avec les nouvelles
        $allSecondaryImages = array_merge($existingImages, $newSecondaryImages);
        if (!empty($allSecondaryImages)) {
            $validated['images'] = $allSecondaryImages;
        } elseif (empty($existingImages) && empty($newSecondaryImages)) {
            // Si aucune image secondaire n'est conservée, on peut laisser null ou un tableau vide
            $validated['images'] = null;
        }

        $validated['in_stock'] = $validated['stock_quantity'] > 0;
        $validated['sale_unit'] = $validated['sale_unit'] ?? 'unit';
        $validated['is_discovery'] = $request->boolean('is_discovery');

        $product->update($validated);

        // Mettre à jour les catégories
        if ($request->filled('categories')) {
            $product->categories()->sync($request->categories);
        } elseif ($request->filled('category_id')) {
            $product->categories()->sync([$request->category_id]);
        }

        // Mettre à jour les attributs
        // Supprimer tous les anciens attributs
        $product->productAttributes()->delete();
        
        // Ajouter les nouveaux attributs
        if ($request->filled('attributes')) {
            $this->saveProductAttributes($product, $request->input('attributes', []));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour avec succès !');
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Product $product)
    {
        // Supprimer l'image principale
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Supprimer les images secondaires
        if ($product->images && is_array($product->images)) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé avec succès !');
    }

    /**
     * Sauvegarder les attributs d'un produit
     */
    private function saveProductAttributes(Product $product, array $attributes)
    {
        foreach ($attributes as $index => $attributeData) {
            $attributeType = AttributeType::findOrFail($attributeData['attribute_type_id']);
            
            // Créer ou récupérer l'attribut du produit
            $productAttribute = ProductAttribute::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'slug' => $attributeType->slug,
                ],
                [
                    'name' => $attributeType->name,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            // Mettre à jour le nom et l'ordre si l'attribut existe déjà
            $productAttribute->update([
                'name' => $attributeType->name,
                'sort_order' => $index,
            ]);

            // Supprimer les anciennes valeurs
            $productAttribute->values()->delete();

            // Ajouter les nouvelles valeurs
            foreach ($attributeData['values'] as $valueOrder => $attributeValueId) {
                $attributeValue = \App\Models\AttributeValue::findOrFail($attributeValueId);
                
                ProductAttributeValue::create([
                    'attribute_id' => $productAttribute->id,
                    'value' => $attributeValue->value,
                    'color_code' => $attributeValue->color_code,
                    'image' => $attributeValue->image,
                    'sort_order' => $valueOrder,
                ]);
            }
        }
    }
}

