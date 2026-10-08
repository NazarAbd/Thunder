<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_accepts_string_amount_without_float_drift(): void
    {
        $user = User::factory()->create();
        $service = app(WalletService::class);

        $service->credit($user, '0.10', 'test');
        $service->credit($user, '0.20', 'test');

        $this->assertSame('0.30', number_format((float) $user->wallet()->first()->balance, 2, '.', ''));
        $this->assertDatabaseHas('wallet_transactions', ['amount' => '0.20']);
    }

    public function test_credit_rejects_zero_negative_and_non_numeric(): void
    {
        $user = User::factory()->create();
        $service = app(WalletService::class);

        foreach (['0', '0.00', '-5', 0, -1.5] as $bad) {
            try {
                $service->credit($user, $bad, 'test');
                $this->fail('Expected RuntimeException for amount: '.var_export($bad, true));
            } catch (RuntimeException) {
                $this->addToAssertionCount(1);
            }
        }

        try {
            $service->credit($user, 'not-a-number', 'test');
            $this->fail('Expected RuntimeException for non-numeric string');
        } catch (RuntimeException) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_debit_insufficient_balance_throws(): void
    {
        $user = User::factory()->create();
        $service = app(WalletService::class);

        $service->credit($user, '50.00', 'topup');

        try {
            $service->debit($user, '50.01', 'purchase');
            $this->fail('Expected insufficient balance exception');
        } catch (RuntimeException $e) {
            $this->assertSame('Insufficient wallet balance.', $e->getMessage());
        }

        $tx = $service->debit($user, '20.00', 'purchase');
        $this->assertSame('30.00', number_format((float) $tx->balance_after, 2, '.', ''));
    }
}
