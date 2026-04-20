<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if user has specific permission
 * 
 * Usage:
 *   Route::post('/dashboard/items/reorder', [ItemController::class, 'reorder'])
 *       ->middleware('check-permission:items.reorder');
 * 
 *   Or in controller:
 *       $this->authorize('items.reorder');
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Check if user is superadmin (gets automatic access)
        if ($user->roles()->where('is_superadmin', true)->exists()) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }

            abort(403, 'Unauthorized. You do not have permission: ' . $permission);
        }

        return $next($request);
    }
}
