<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::with('user')->get();
        return view('backend.pages.brands.index', compact('brands'));
    }
     public function view($id)
    {
        $brand = Brand::with('user')->findOrFail($id);
        return view('backend.pages.brands.view', compact('brand'));
    }

    public function create()
    {
        return view('backend.pages.brands.create');
    }

    public function createModerator()
    {
        return view('backend.pages.brands.create-moderator');
    }
}
