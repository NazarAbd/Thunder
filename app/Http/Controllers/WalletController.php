<?php

namespace App\Http\Controllers;

use App\Models\TopUpRequest;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $walletService) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // Opening the wallet marks everything as read (never deleted).
        // Clearing (deleting) is only ever manual via the bell dropdown.
        $user->unreadNotifications()->update(['read_at' => now()]);

        $wallet = $this->walletService->walletFor($user);

        $topUpRequests = $user->topUpRequests()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('wallet.index', [
            'wallet' => $wallet,
            'topUpRequests' => $topUpRequests,
        ]);
    }

    /**
     * Entry point used by notification clicks. Works out which page of the
     * paginated history (10 per page, same as index() above) this specific
     * top-up request falls on, then redirects there with a "#topup-{id}"
     * fragment so the browser scrolls to it and the CSS in wallet/index.blade.php
     * highlights it briefly.
     */
    public function showTopup(Request $request, TopUpRequest $topUpRequest): RedirectResponse
    {
        abort_unless($topUpRequest->user_id === $request->user()->id, 403);

        $perPage = 10; // Must match paginate(10) in index() above.

        // Count how many of this user's requests sort before-or-at this one,
        // using the exact same order as index(): created_at desc, then id desc.
        // That count is this request's 1-based rank, which tells us its page.
        $position = TopUpRequest::where('user_id', $request->user()->id)
            ->where(function ($query) use ($topUpRequest) {
                $query->where('created_at', '>', $topUpRequest->created_at)
                    ->orWhere(function ($query) use ($topUpRequest) {
                        $query->where('created_at', $topUpRequest->created_at)
                            ->where('id', '>=', $topUpRequest->id);
                    });
            })
            ->count();

        $page = max((int) ceil($position / $perPage), 1);

        return redirect(route('wallet.index', ['page' => $page]) . '#topup-' . $topUpRequest->id);
    }
}
