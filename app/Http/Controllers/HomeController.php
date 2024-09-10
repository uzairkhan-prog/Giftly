<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\AdminHomePageSection;
use Illuminate\View\View;

class HomeController extends Controller
{
    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function shop(): View
    {
        $products = Product::latest()->paginate(5);

        return view('shop', compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function contact()
    {
        return view('contact');
    }

    public function why()
    {
        return view('why');
    }

    public function showHomePageSection()
    {
        // $topUsers = User::latest()->paginate(3);

        $roles = ['Admin', 'Editor', 'Super Admin'];
        $topUsers = User::whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles);
        })->latest()->paginate(3);

        // Fetch all enabled sections
        $enabledSections = AdminHomePageSection::where('is_enabled', true)->pluck('name')->toArray();

        $products = [];
        if (in_array('Latest Products', $enabledSections)) {
            $products = Product::latest()->paginate(5);
        }

        return view('welcome', compact('enabledSections', 'products', 'topUsers', 'roles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}
