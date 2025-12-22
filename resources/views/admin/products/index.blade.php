@extends('dashboard.layout')

@section('title', 'Gestion des Produits - ShopMe')
@section('page-title', 'Gestion des Produits')

@section('content')
<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
<div class="space-y-4">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Total Produits</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $products->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-box text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Produits Actifs</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ \App\Models\Product::where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Produits Inactifs</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">{{ \App\Models\Product::where('status', 'inactive')->count() }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <i class="fas fa-pause-circle text-gray-600 text-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">En Rupture</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ \App\Models\Product::where('stock_quantity', 0)->count() }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec bouton d'ajout et actions en masse -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Liste des Produits</h2>
                <p class="text-sm text-gray-600">Gérez vos produits et leurs statuts</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Actions en masse (masquées par défaut) -->
                <div id="bulk-actions" class="hidden flex items-center gap-2">
                    <span id="selected-count" class="text-sm text-gray-700 font-medium">0 sélectionné(s)</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="bulkAction('activate')" class="bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600 text-sm font-medium">
                            <i class="fas fa-check mr-1"></i> Activer
                        </button>
                        <button type="button" onclick="bulkAction('deactivate')" class="bg-gray-500 text-white px-3 py-1.5 rounded-lg hover:bg-gray-600 text-sm font-medium">
                            <i class="fas fa-pause mr-1"></i> Désactiver
                        </button>
                        <button type="button" onclick="bulkAction('delete')" class="bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600 text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i> Supprimer
                        </button>
                    </div>
                    <button type="button" onclick="clearSelection()" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
                <a href="{{ route('admin.products.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-semibold">
                    <i class="fas fa-plus mr-1"></i> Ajouter un produit
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, SKU, description..." 
                       class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Catégorie</label>
                <select name="category" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Toutes</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-blue-600">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Pagination en haut -->
    @if($products->hasPages())
    <div class="bg-white rounded-lg shadow p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }} sur {{ $products->total() }} produits
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- Tableau des produits -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="select-all" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" onchange="toggleSelectAll(this)">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="selected_products[]" value="{{ $product->id }}" 
                                       class="product-checkbox w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500" 
                                       onchange="updateBulkActions()">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                         class="w-12 h-12 object-cover rounded border border-gray-200">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center border border-gray-200">
                                        <i class="fas fa-image text-gray-400 text-xs"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($product->short_description ?? $product->description, 50) }}</div>
                                    </div>
                                    @if($product->featured)
                                        <span class="px-1.5 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">⭐</span>
                                    @endif
                                    @if($product->is_discovery)
                                        <span class="px-1.5 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">🔍</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $product->sku ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($product->is_on_sale && $product->sale_price)
                                        <span class="text-red-600">{{ number_format($product->sale_price, 0, '', '.') }} FCFA</span>
                                        <span class="text-gray-400 line-through text-xs ml-2">{{ number_format($product->price, 0, '', '.') }} FCFA</span>
                                    @else
                                        {{ number_format($product->price, 0, '', '.') }} FCFA
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $product->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $product->status == 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="text-blue-600 hover:text-blue-900 p-1.5 rounded hover:bg-blue-50" 
                                       title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="text-orange-600 hover:text-orange-900 p-1.5 rounded hover:bg-orange-50" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1.5 rounded hover:bg-red-50" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <i class="fas fa-box-open text-4xl mb-2 text-gray-300"></i>
                                    <p class="text-sm">Aucun produit trouvé</p>
                                    <a href="{{ route('admin.products.create') }}" class="mt-3 text-orange-600 hover:text-orange-700 text-sm font-medium">
                                        Créer votre premier produit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }} sur {{ $products->total() }} produits
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
</div>

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        const count = checkboxes.length;
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');
        
        if (count > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = count + ' sélectionné(s)';
        } else {
            bulkActions.classList.add('hidden');
        }
        
        // Mettre à jour la case "Tout sélectionner"
        const selectAll = document.getElementById('select-all');
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateBulkActions();
    }
    
    function updateStatistics() {
        // Les statistiques sont calculées côté serveur et affichées au chargement de la page
        // Pour une mise à jour en temps réel, on pourrait faire une requête AJAX
        // mais pour l'instant, on laisse les statistiques telles quelles
    }

    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        const productIds = Array.from(checkboxes).map(cb => cb.value);
        
        if (productIds.length === 0) {
            showNotification('Veuillez sélectionner au moins un produit', 'error');
            return;
        }
        
        let confirmMessage = '';
        switch(action) {
            case 'activate':
                confirmMessage = `Êtes-vous sûr de vouloir activer ${productIds.length} produit(s) ?`;
                break;
            case 'deactivate':
                confirmMessage = `Êtes-vous sûr de vouloir désactiver ${productIds.length} produit(s) ?`;
                break;
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer ${productIds.length} produit(s) ? Cette action est irréversible !`;
                break;
        }
        
        if (!confirm(confirmMessage)) {
            return;
        }
        
        // Désactiver les boutons pendant le traitement
        const buttons = document.querySelectorAll('#bulk-actions button');
        buttons.forEach(btn => btn.disabled = true);
        
        // Envoyer la requête AJAX
        fetch('{{ route("admin.products.bulk-action") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                product_ids: JSON.stringify(productIds)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                
                // Mettre à jour le DOM sans recharger la page
                if (action === 'delete') {
                    // Supprimer les lignes du tableau
                    productIds.forEach(id => {
                        const checkbox = document.querySelector(`input.product-checkbox[value="${id}"]`);
                        if (checkbox) {
                            const row = checkbox.closest('tr');
                            if (row) {
                                row.style.transition = 'opacity 0.3s';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 300);
                            }
                        }
                    });
                } else {
                    // Mettre à jour les statuts dans le tableau
                    productIds.forEach(id => {
                        const checkbox = document.querySelector(`input.product-checkbox[value="${id}"]`);
                        if (checkbox) {
                            const row = checkbox.closest('tr');
                            if (row) {
                                const statusCell = row.querySelector('td:nth-child(7)');
                                if (statusCell) {
                                    const newStatus = action === 'activate' ? 'active' : 'inactive';
                                    const statusText = action === 'activate' ? 'Actif' : 'Inactif';
                                    const bgClass = action === 'activate' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                    
                                    statusCell.innerHTML = `<span class="px-2 py-1 text-xs rounded-full font-medium ${bgClass}">${statusText}</span>`;
                                }
                            }
                        }
                    });
                }
                
                // Mettre à jour les statistiques
                updateStatistics();
                
                // Réinitialiser la sélection
                clearSelection();
            } else {
                showNotification(data.message || 'Une erreur est survenue', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Une erreur est survenue lors de l\'opération', 'error');
        })
        .finally(() => {
            // Réactiver les boutons
            buttons.forEach(btn => btn.disabled = false);
        });
    }
    
    function showNotification(message, type) {
        // Créer ou récupérer le conteneur de notification
        let notificationContainer = document.getElementById('notification-container');
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'notification-container';
            notificationContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(notificationContainer);
        }
        
        // Créer la notification
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        notification.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] animate-slide-in`;
        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        notificationContainer.appendChild(notification);
        
        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            notification.style.transition = 'opacity 0.3s';
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }
</script>
@endsection
