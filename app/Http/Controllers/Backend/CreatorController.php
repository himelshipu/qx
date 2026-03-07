<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CreatorController extends Controller
{
    public function index()
    {
        $creators = \App\Models\Creator::all();
        return view('backend.pages.creators.index', compact('creators'));
    }

    public function create()
    {
        return view('backend.pages.creators.create');
    }

    public function view($id)
    {
        $creator = Creator::findOrFail($id);

        return view('backend.pages.creators.view', compact('creator'));
    }
}
