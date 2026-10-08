<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Freeze blocked accounts (users.status = false).
     *
     * Blocked users may still browse public pages, open /account/frozen,
     * read /contact (WhatsApp support) and POST /logout — everything else
     * behind `auth` redirects to the frozen notice. Their wallet is
     * additionally guarded in WalletService + TopUpRequestController.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBlocked()) {
            $allowed = ['logout', 'account.frozen', 'contact', 'about', 'home'];

            if (! $request->routeIs($allowed)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'الحساب موقوف. تواصل مع الدعم عبر واتساب.'], 403);
                }

                return redirect()->route('account.frozen');
            }
        }

        return $next($request);
    }
}
