<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $walletService) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $wallet = $this->walletService->walletFor($user);

        $topUpRequests = $user->topUpRequests()
            ->latest()
            ->paginate(10);

        return view('wallet.index', [
            'wallet' => $wallet,
            'topUpRequests' => $topUpRequests,
        ]);
    }
}
