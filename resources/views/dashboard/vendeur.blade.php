@extends('dashboard.layout')

@section('title', 'Dashboard Vendeur - ShopMe')
@section('page-title', 'Dashboard Vendeur')

@section('content')
<div class="space-y-6">
    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Produits</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProducts }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-box text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Commandes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalOrders }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-shopping-bag text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Revenus Totaux</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalRevenue, 2) }} €</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-euro-sign text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Commandes en attente</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingOrders }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-clock text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des revenus mensuels -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Revenus Mensuels</h2>
        <canvas id="monthlyRevenueChart" height="80"></canvas>
    </div>

    <!-- Produits les plus vendus et Commandes récentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Produits les plus vendus -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Produits les plus vendus</h2>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $product->sales_count }} ventes</p>
                        </div>
                    </div>
                    <p class="font-bold text-orange-600">{{ number_format($product->price, 2) }} €</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucun produit vendu</p>
                @endforelse
            </div>
        </div>

        <!-- Commandes récentes -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Commandes récentes</h2>
            <div class="space-y-4">
                @forelse($recentOrders as $order)
                <div class="border-l-4 border-orange-500 p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-gray-800">#{{ $order->order_number }}</p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status == 'delivered') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $order->user->name }}</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ number_format($order->total, 2) }} €</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucune commande récente</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Graphique des revenus mensuels
    const monthlyData = @json($monthlyStats);
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    const revenueData = new Array(12).fill(0);
    
    monthlyData.forEach(stat => {
        revenueData[stat.month - 1] = parseFloat(stat.revenue);
    });

    const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenus (€)',
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
                            return value + ' €';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection

