<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        return view('backend.pages.campaigns.index');
    }

    public function create()
    {
        return view('backend.pages.campaigns.create');
    }
}
