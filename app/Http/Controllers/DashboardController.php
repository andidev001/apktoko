<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalSales = Transaction::where('status', 'lunas')->sum('total_harga');
        $todaySales = Transaction::where('status', 'lunas')->whereDate('created_at', today())->sum('total_harga');
        $totalTransactions = Transaction::where('status', 'lunas')->count();
        $recentTransactions = Transaction::where('status', 'lunas')->orderBy('created_at', 'desc')->take(5)->get();
        $lowStocks = Product::where('stok', '<=', 10)->orderBy('stok', 'asc')->take(5)->get();

        return view('dashboard', compact('totalProducts', 'totalSales', 'todaySales', 'totalTransactions', 'recentTransactions', 'lowStocks'));
    }
}
