<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_without_games_and_with_active_games(): void
    {
        $this->get('/')->assertStatus(200);

        Game::create([
            'slug' => 'test-game',
            'name' => 'Test Game',
            'description' => 'desc',
            'group' => 'direct',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->get('/')->assertStatus(200)->assertSee('Test Game');
    }

    public function test_game_show_404_for_unknown_and_inactive(): void
    {
        $this->get('/games/nope')->assertStatus(404);

        Game::create([
            'slug' => 'hidden-game',
            'name' => 'Hidden',
            'description' => 'desc',
            'group' => 'direct',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $this->get('/games/hidden-game')->assertStatus(404);
    }

    public function test_topup_rejects_inactive_bank_account(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $inactive = BankAccount::create([
            'bank_name' => 'Old Bank',
            'account_holder_name' => 'Holder',
            'account_number' => '123',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->post('/wallet/topup', [
            'bank_account_id' => $inactive->id,
            'amount' => '100.00',
            'receipt' => UploadedFile::fake()->create('receipt.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('bank_account_id');
        $this->assertDatabaseMissing('top_up_requests', ['user_id' => $user->id]);
    }

    public function test_topup_blocks_second_pending_request(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $bank = BankAccount::create([
            'bank_name' => 'Bank',
            'account_holder_name' => 'Holder',
            'account_number' => '456',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post('/wallet/topup', [
            'bank_account_id' => $bank->id,
            'amount' => '100.00',
            'receipt' => UploadedFile::fake()->create('r1.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('wallet.index'));

        $this->actingAs($user)->post('/wallet/topup', [
            'bank_account_id' => $bank->id,
            'amount' => '50.00',
            'receipt' => UploadedFile::fake()->create('r2.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('wallet.index'));

        $this->assertSame(1, $user->topUpRequests()->count());
    }

    public function test_google_redirect_404_when_not_configured(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/auth/google/redirect')->assertStatus(404);
    }
}
