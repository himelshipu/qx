<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Workflow B (Package Order): Redirect to conversation after login if confirming package
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if user was confirming a package order
        $pendingConversationId = session('pending_conversation_id');
        $pendingCreatorId      = session('pending_creator_id');
        $pendingPackageId      = session('pending_package_id');

        if ($pendingConversationId) {
            // Clear the session data
            session()->forget(['pending_conversation_id', 'pending_creator_id', 'pending_package_id']);

            // Redirect to the conversation

            return redirect()->route('conversations.show', \App\Models\Conversation::findOrFail($pendingConversationId));
        }

        if ($pendingCreatorId && $pendingPackageId) {
            // Create conversation for package order and redirect
            session()->forget(['pending_creator_id', 'pending_package_id']);

            $conversation = \App\Http\Controllers\ConversationController::createForPackageOrder(
                auth()->id(),
                $pendingCreatorId,
                null// order_id will be created during checkout
            );

            return redirect()->route('conversations.show', $conversation);
        }

        return redirect()->intended(route('dashboard.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
