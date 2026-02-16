<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

/**
 * Class DashboardController
 *
 * Handles admin dashboard operations.
 */
final class DashboardController extends AdminController
{
    /**
     * Display the admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'pageTitle' => 'Dashboard',
        ]);
    }
}
