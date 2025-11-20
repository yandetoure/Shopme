@extends('dashboard.layout')

@section('title', 'Modifier le Produit - ShopMe')
@section('page-title', 'Modifier le Produit')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom du produit *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description courte -->
                <div class="md:col-span-2">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">Description courte</label>
                    <textarea id="short_description" name="short_description" rows="2"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- SKU -->
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Catégorie principale -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie principale</label>
                    <select id="category_id" name="category_id"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Catégories multiples -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catégories</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                       class="mr-2" {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Prix -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="1" min="0" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Prix de vente -->
                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">Prix de vente (FCFA)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="1" min="0"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- En promotion -->
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="is_on_sale" value="1" {{ old('is_on_sale', $product->is_on_sale) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm font-medium text-gray-700">En promotion</span>
                    </label>
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantité en stock *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Poids -->
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Poids (kg)</label>
                    <input type="number" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <!-- En vedette -->
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm font-medium text-gray-700">Mettre en vedette</span>
                    </label>
                </div>

                <!-- Image actuelle -->
                @if($product->image)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image actuelle</label>
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded">
                </div>
                @endif

                <!-- Nouvelle image -->
                <div class="md:col-span-2">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Nouvelle image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
            </div>

            <!-- Section Attributs/Variables -->
            <div class="md:col-span-2 mt-6 border-t pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-gray-800">Attributs/Variables</h3>
                    <button type="button" onclick="addAttribute()" class="bg-blue-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-blue-600">
                        <i class="fas fa-plus mr-1"></i> Ajouter un attribut
                    </button>
                </div>
                <div id="attributes-container" class="space-y-4">
                    <!-- Les attributs existants seront chargés via JavaScript -->
                </div>
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Charger les attributs existants du produit
    const existingAttributes = @json($existingAttributes ?? []);

    const attributeTypes = @json($attributeTypesJson ?? []);

    let attributeIndex = 0;

    function addAttribute(attributeTypeId = null, selectedValues = []) {
        const container = document.getElementById('attributes-container');
        const index = attributeIndex++;
        
        const attributeDiv = document.createElement('div');
        attributeDiv.className = 'p-4 border rounded-lg bg-gray-50';
        attributeDiv.dataset.index = index;

        let attributeTypeSelect = '<select name="attributes[' + index + '][attribute_type_id]" class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500" onchange="updateAttributeValues(' + index + ', this.value)" required>';
        attributeTypeSelect += '<option value="">Sélectionner un type d\'attribut</option>';
        
        attributeTypes.forEach(type => {
            const selected = attributeTypeId && type.id == attributeTypeId ? 'selected' : '';
            attributeTypeSelect += '<option value="' + type.id + '" ' + selected + '>' + type.name + '</option>';
        });
        attributeTypeSelect += '</select>';

        let valuesHtml = '<div id="attribute-values-' + index + '" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-2"></div>';
        
        // Initialiser les valeurs si un type est déjà sélectionné
        if (attributeTypeId) {
            setTimeout(() => {
                updateAttributeValues(index, attributeTypeId);
                if (selectedValues && selectedValues.length > 0) {
                    setTimeout(() => {
                        selectedValues.forEach(valueId => {
                            const checkbox = document.querySelector(`input[name="attributes[${index}][values][]"][value="${valueId}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }, 100);
                }
            }, 100);
        }

        attributeDiv.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 mr-3">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Type d'attribut</label>
                    ${attributeTypeSelect}
                </div>
                <button type="button" onclick="removeAttribute(${index})" class="mt-6 text-red-600 hover:text-red-800">
                    <i class="fas fa-trash text-sm"></i>
                </button>
            </div>
            ${valuesHtml}
        `;

        container.appendChild(attributeDiv);
    }

    function updateAttributeValues(index, attributeTypeId) {
        const valuesContainer = document.getElementById('attribute-values-' + index);
        valuesContainer.innerHTML = '';

        const attributeType = attributeTypes.find(t => t.id == attributeTypeId);
        if (!attributeType || !attributeType.values || attributeType.values.length === 0) {
            valuesContainer.innerHTML = '<p class="text-xs text-gray-500 col-span-full">Aucune valeur disponible pour ce type d\'attribut. <a href="/admin/variables" target="_blank" class="text-blue-600 hover:underline">Ajouter des valeurs</a></p>';
            return;
        }

        valuesContainer.innerHTML = '<label class="block text-xs font-medium text-gray-700 mb-2 col-span-full">Sélectionner les valeurs</label>';

        attributeType.values.forEach(value => {
            const valueDiv = document.createElement('div');
            valueDiv.className = 'flex items-center space-x-2 p-2 border rounded hover:bg-gray-100';

            let visualElement = '';
            if (attributeType.type === 'color' && value.color_code) {
                visualElement = `<div class="w-6 h-6 rounded-full border border-gray-300 flex-shrink-0" style="background-color: ${value.color_code}"></div>`;
            } else if (attributeType.type === 'image' && value.image) {
                visualElement = `<img src="${value.image}" alt="${value.value}" class="w-6 h-6 object-cover rounded flex-shrink-0">`;
            }

            valueDiv.innerHTML = `
                <label class="flex items-center space-x-2 cursor-pointer flex-1">
                    <input type="checkbox" name="attributes[${index}][values][]" value="${value.id}" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    ${visualElement}
                    <span class="text-xs text-gray-700">${value.value}</span>
                </label>
            `;

            valuesContainer.appendChild(valueDiv);
        });
    }

    function removeAttribute(index) {
        const container = document.getElementById('attributes-container');
        const attributeDiv = container.querySelector(`[data-index="${index}"]`);
        if (attributeDiv) {
            attributeDiv.remove();
        }
    }

    // Charger les attributs existants au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        if (existingAttributes && existingAttributes.length > 0) {
            existingAttributes.forEach(function(attr) {
                if (attr && attr.attribute_type_id) {
                    addAttribute(attr.attribute_type_id, attr.selected_values || []);
                }
            });
        }
    });
</script>
@endsection

