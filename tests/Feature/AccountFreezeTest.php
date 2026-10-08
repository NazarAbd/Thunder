<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class AccountFreezeTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_user_is_redirected_to_frozen_notice(): void
    {
        $user = User::factory()->create(['status' => false]);

        $this->actingAs($user)->get('/wallet')->assertRedirect(route('account.frozen'));
        $this->actingAs($user)->get('/account/frozen')->assertStatus(200)->assertSee('موقوف');
    }

    public function test_active_user_bounces_off_frozen_page(): void
    {
        $user = User::factory()->create(['status' => true]);

        $this->actingAs($user)->get('/account/frozen')->assertRedirect(route('home'));
    }

    public function test_blocked_user_cannot_submit_topup(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['status' => false]);
        $bank = BankAccount::create([
            'bank_name' => 'Bank',
            'account_holder_name' => 'Holder',
            'account_number' => '789',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post('/wallet/topup', [
            'bank_account_id' => $bank->id,
            'amount' => '100.00',
            'receipt' => UploadedFile::fake()->create('r.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('account.frozen'));

        $this->assertSame(0, $user->topUpRequests()->count());
    }

    public function test_wallet_service_refuses_blocked_users(): void
    {
        $user = User::factory()->create(['status' => false]);
        $service = app(WalletService::class);

        try {
            $service->credit($user, '10.00', 'test');
            $this->fail('Expected block exception on credit');
        } catch (RuntimeException) {
            $this->addToAssertionCount(1);
        }

        try {
            $service->debit($user, '10.00', 'test');
            $this->fail('Expected block exception on debit');
        } catch (RuntimeException) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_whatsapp_float_appears_when_configured(): void
    {
        config(['store.whatsapp' => '249900000000']);

        $this->get('/contact')->assertStatus(200)->assertSee('wa.me/249900000000');
        $this->get('/')->assertStatus(200)->assertSee('wa.me/249900000000');
    }

    public function test_whatsapp_float_hidden_when_not_configured(): void
    {
        config(['store.whatsapp' => '']);

        $this->get('/contact')->assertStatus(200)->assertDontSee('wa.me/');
    }
}
