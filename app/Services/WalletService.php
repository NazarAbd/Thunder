<?php

namespace App\Services;

use App\Enums\WalletTransactionType;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    /**
     * Get (or lazily create) the wallet belonging to a user.
     * This is a safety net for any user that existed before wallets were introduced.
     */
    public function walletFor(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0],
        );
    }

    /**
     * Add money to a user's wallet and record the ledger entry.
     */
    public function credit(User $user, float $amount, string $description, ?object $reference = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $description, $reference) {
            $wallet = $this->lockedWalletFor($user);

            $newBalance = bcadd((string) $wallet->balance, (string) $amount, 2);
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::Credit,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
            ]);
        });
    }

    /**
     * Subtract money from a user's wallet and record the ledger entry.
     * Not wired to any feature yet (no purchase flow in this phase), but built now
     * so the next phase (spending wallet balance on a real order) has a safe primitive to call.
     */
    public function debit(User $user, float $amount, string $description, ?object $reference = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $description, $reference) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $wallet || bccomp((string) $wallet->balance, (string) $amount, 2) < 0) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $newBalance = bcsub((string) $wallet->balance, (string) $amount, 2);
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::Debit,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
            ]);
        });
    }

    /**
     * Fetch a user's wallet row locked "FOR UPDATE" for the rest of the
     * enclosing transaction, creating it first if it doesn't exist yet.
     *
     * The tricky part: lockForUpdate() only locks rows that already exist.
     * If a wallet doesn't exist yet (e.g. a seeded user whose UserObserver
     * never fired because the seeder used WithoutModelEvents), two
     * concurrent credit() calls could both see "no wallet" and both try to
     * INSERT one — and the wallets table's unique(user_id) constraint means
     * one of those inserts fails outright. We catch that specific failure
     * and simply re-fetch-and-lock the row the other request just created,
     * instead of letting the whole credit() call error out and roll back.
     */
    private function lockedWalletFor(User $user): Wallet
    {
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if ($wallet) {
            return $wallet;
        }

        try {
            return Wallet::create(['user_id' => $user->id, 'balance' => 0]);
        } catch (UniqueConstraintViolationException) {
            return Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
        }
    }
}
resources / views / filament / schemas