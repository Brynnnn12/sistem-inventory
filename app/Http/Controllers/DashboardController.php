<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display dashboard with products and employees data.
     */
    public function index(): Response
    {
        // Get latest 5 products with category
        $products = \App\Models\Product::query()
            ->with(['category:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'unit' => $product->unit,
                    'price' => $product->price,
                    'is_active' => $product->is_active,
                    'image_url' => $product->image_url,
                    'category' => $product->category,
                ];
            });

        // Get latest 5 employees
        $employees = \App\Models\User::query()
            ->select('id', 'name', 'email', 'created_at')
            ->where('is_active', true)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()?->name,
                    'is_active' => $user->is_active ?? true,
                    'created_at' => $user->created_at,
                ];
            });

        return Inertia::render('dashboard', [
            'products' => $products,
            'employees' => $employees,
        ]);
    }
}
