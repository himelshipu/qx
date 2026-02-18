<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CreatorController extends Controller
{
    public function index()
    {
        return view('backend.pages.creators.index');
    }
}
