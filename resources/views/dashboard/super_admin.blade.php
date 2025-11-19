@extends('dashboard.layout')

@section('title', 'Dashboard Super Admin - ShopMe')
@section('page-title', 'Dashboard Super Admin')

@section('content')
<div class="space-y-4">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs">Total Utilisateurs</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</p>
                    <div class="flex space-x-3 mt-1 text-xs">
                        <span class="text-blue-600">{{ $totalAdmins }} admins</span>
                        <span class="text-green-600">{{ $totalVendeurs }} vendeurs</span>
                        <span class="text-gray-600">{{ $totalClients }} clients</span>
                    </div>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs">Total Produits</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-box text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs">Revenus Totaux</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totalRevenue, 0, '', '.') }} FCFA</p>
                    <p class="text-xs text-green-600 mt-1">Ce mois: {{ number_format($monthlyRevenue, 0, '', '.') }} FCFA</p>
                    <p class="text-xs text-gray-500 mt-1">Cette année: {{ number_format($yearlyRevenue, 0, '', '.') }} FCFA</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-money-bill text-yellow-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs">Total Commandes</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOrders }}</p>
                    <p class="text-xs text-orange-600 mt-1">{{ $pendingOrders }} en attente</p>
                    <p class="text-xs text-green-600 mt-1">{{ $completedOrders }} complétées</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-shopping-bag text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques secondaires -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs">Catégories</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $totalCategories }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs">Admins</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ $totalAdmins }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs">Vendeurs</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ $totalVendeurs }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs">Commandes annulées</p>
            <p class="text-xl font-bold text-red-600 mt-1">{{ $cancelledOrders }}</p>
        </div>
    </div>

    <!-- Graphiques et statistiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Commandes par statut</h2>
            <canvas id="ordersByStatusChart" height="200"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Répartition des utilisateurs par rôle</h2>
            <canvas id="usersByRoleChart" height="200"></canvas>
        </div>
    </div>

    <!-- Graphique des revenus mensuels -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Revenus Mensuels (12 derniers mois)</h2>
        <canvas id="monthlyRevenueChart" height="80"></canvas>
    </div>

    <!-- Utilisateurs récents et Commandes récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Utilisateurs récents -->
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Utilisateurs récents</h2>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucun utilisateur récent</p>
                @endforelse
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Commandes récentes</h2>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                <div class="border-l-4 border-orange-500 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">#{{ $order->order_number }}</p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                    <p class="text-xs font-bold text-gray-800 mt-1">{{ number_format($order->total, 0, '', '.') }} FCFA</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucune commande récente</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Produits les plus vendus -->
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Top 10 Produits les plus vendus</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ventes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProducts as $product)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->price, 0, '', '.') }} FCFA</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->sales_count }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-orange-600">{{ number_format($product->price * $product->sales_count, 0, '', '.') }} FCFA</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Aucun produit vendu</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Graphique des commandes par statut
    const ordersByStatus = @json($ordersByStatus);
    const statusLabels = ordersByStatus.map(item => item.status);
    const statusCounts = ordersByStatus.map(item => item.count);
    
    const ctx1 = document.getElementById('ordersByStatusChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: [
                    'rgb(249, 115, 22)',
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(239, 68, 68)',
                    'rgb(156, 163, 175)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Graphique des utilisateurs par rôle
    const usersByRole = @json($usersByRole);
    const roleLabels = Object.keys(usersByRole);
    const roleCounts = Object.values(usersByRole);
    
    const ctx2 = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: roleLabels.map(role => role.charAt(0).toUpperCase() + role.slice(1)),
            datasets: [{
                data: roleCounts,
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)',
                    'rgb(156, 163, 175)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Graphique des revenus mensuels
    const monthlyData = @json($monthlyStats);
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    const monthLabels = [];
    const revenueData = [];
    
    // Créer un tableau avec 12 mois de données
    const currentMonth = new Date().getMonth() + 1;
    const currentYear = new Date().getFullYear();
    
    for (let i = 11; i >= 0; i--) {
        const monthIndex = (currentMonth - i - 1 + 12) % 12;
        monthLabels.push(months[monthIndex]);
        
        // Trouver les données correspondantes
        const data = monthlyData.find(d => {
            const dataMonth = parseInt(d.month);
            const dataYear = parseInt(d.year);
            let targetMonth = currentMonth - i - 1;
            let targetYear = currentYear;
            
            if (targetMonth <= 0) {
                targetMonth += 12;
                targetYear -= 1;
            }
            
            return dataMonth === targetMonth && dataYear === targetYear;
        });
        
        revenueData.push(data ? parseFloat(data.revenue) : 0);
    }

    const ctx3 = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Revenus (FCFA)',
                data: revenueData,
                borderColor: 'rgb(249, 115, 22)',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection

