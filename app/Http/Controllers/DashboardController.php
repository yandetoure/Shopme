<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard du vendeur
     */
    public function vendeur()
    {
        $user = Auth::user();
        
        // Statistiques pour le vendeur
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Commandes récentes
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();
        
        // Produits les plus vendus
        $topProducts = Product::orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
        
        // Statistiques mensuelles
        $monthlyStats = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        return view('dashboard.vendeur', compact(
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'recentOrders',
            'topProducts',
            'monthlyStats'
        ));
    }

    /**
     * Dashboard de l'admin
     */
    public function admin()
    {
        $user = Auth::user();
        
        // Statistiques générales
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        
        // Revenus
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total');
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total');
        
        // Commandes
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        
        // Utilisateurs récents
        $recentUsers = User::latest()->take(5)->get();
        
        // Commandes récentes
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();
        
        // Produits les plus vendus
        $topProducts = Product::orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
        
        // Statistiques par statut de commande
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'totalRevenue',
            'monthlyRevenue',
            'pendingOrders',
            'completedOrders',
            'recentUsers',
            'recentOrders',
            'topProducts',
            'ordersByStatus'
        ));
    }

    /**
     * Dashboard du super admin
     */
    public function superAdmin()
    {
        $user = Auth::user();
        
        // Statistiques globales
        $totalUsers = User::count();
        $totalAdmins = User::role('admin')->count();
        $totalVendeurs = User::role('vendeur')->count();
        $totalClients = User::role('client')->count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        
        // Revenus
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total');
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total');
        $yearlyRevenue = Order::where('payment_status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->sum('total');
        
        // Commandes
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        // Utilisateurs récents
        $recentUsers = User::with('roles')->latest()->take(10)->get();
        
        // Commandes récentes
        $recentOrders = Order::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();
        
        // Produits les plus vendus
        $topProducts = Product::orderBy('sales_count', 'desc')
            ->take(10)
            ->get();
        
        // Statistiques par statut de commande
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Statistiques mensuelles (12 derniers mois)
        $monthlyStats = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total) as revenue')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', '>=', date('Y', strtotime('-12 months')))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Statistiques par rôle
        $usersByRole = [
            'admin' => $totalAdmins,
            'vendeur' => $totalVendeurs,
            'client' => $totalClients,
        ];

        return view('dashboard.super_admin', compact(
            'totalUsers',
            'totalAdmins',
            'totalVendeurs',
            'totalClients',
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'totalRevenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'pendingOrders',
            'completedOrders',
            'cancelledOrders',
            'recentUsers',
            'recentOrders',
            'topProducts',
            'ordersByStatus',
            'monthlyStats',
            'usersByRole'
        ));
    }
}

