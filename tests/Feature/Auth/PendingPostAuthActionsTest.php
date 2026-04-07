<?php

use App\Models\CartItem;
use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Package;
use App\Models\User;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->makeUser = function (string $email, string $type = 'brand'): User {
        return User::create([
            'name' => ucfirst($type) . ' User',
            'email' => $email,
            'password' => 'password',
            'user_type' => $type,
        ]);
    };

    $this->makeCreatorWithPackage = function (): array {
        $influencerUser = ($this->makeUser)('creator@example.com', 'creator');

        $influencer = Influencer::create([
            'user_id' => $influencerUser->id,
            'display_name' => 'Creator One',
        ]);

        $package = Package::create([
            'creator_id' => $influencer->id,
            'platform' => 'instagram',
            'name' => 'Instagram Story',
            'base_price' => 120,
            'currency' => 'USD',
        ]);

        return [$influencer, $package];
    };
});

test('guest add to cart gets login redirect metadata and stores pending action', function () {
    [, $package] = ($this->makeCreatorWithPackage)();

    $response = $this->postJson(route('cart.add'), [
        'package_id' => $package->id,
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'requires_auth' => true,
            'redirect_url' => route('login'),
        ]);

    $this->assertSame(PendingPostAuthActionService::ACTION_ADD_TO_CART, session(PendingPostAuthActionService::ACTION_KEY));
    $this->assertSame($package->id, session(PendingPostAuthActionService::PACKAGE_ID_KEY));
});

test('pending add to cart is consumed after login and redirects to cart', function () {
    $brand = ($this->makeUser)('brand@example.com', 'brand');
    [, $package] = ($this->makeCreatorWithPackage)();

    session()->put(PendingPostAuthActionService::ACTION_KEY, PendingPostAuthActionService::ACTION_ADD_TO_CART);
    session()->put(PendingPostAuthActionService::PACKAGE_ID_KEY, $package->id);

    $response = $this->post('/login', [
        'email' => $brand->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('cart.index', absolute: false));

    $this->assertDatabaseHas('cart_items', [
        'package_id' => $package->id,
        'creator_id' => $package->creator_id,
    ]);

    $this->assertNull(session(PendingPostAuthActionService::ACTION_KEY));
    $this->assertNull(session(PendingPostAuthActionService::PACKAGE_ID_KEY));
});

test('guest negotiation request stores pending action and redirects to login', function () {
    [$influencer] = ($this->makeCreatorWithPackage)();

    $response = $this->get(route('conversations.start-negotiation', ['creator' => $influencer->id]));

    $response->assertRedirect(route('login', absolute: false));

    $this->assertSame(PendingPostAuthActionService::ACTION_NEGOTIATE, session(PendingPostAuthActionService::ACTION_KEY));
    $this->assertSame($influencer->id, session(PendingPostAuthActionService::CREATOR_ID_KEY));
});

test('pending negotiation is consumed after login and redirects to conversation', function () {
    $brand = ($this->makeUser)('brand-negotiate@example.com', 'brand');
    [$influencer] = ($this->makeCreatorWithPackage)();

    session()->put(PendingPostAuthActionService::ACTION_KEY, PendingPostAuthActionService::ACTION_NEGOTIATE);
    session()->put(PendingPostAuthActionService::CREATOR_ID_KEY, $influencer->id);

    $response = $this->post('/login', [
        'email' => $brand->email,
        'password' => 'password',
    ]);

    $conversation = Conversation::query()
        ->where('brand_user_id', $brand->id)
        ->where('creator_id', $influencer->id)
        ->first();

    expect($conversation)->not->toBeNull();

    $response->assertRedirect(route('dashboard.conversations.show', ['conversation' => $conversation], false));
});

test('normal login without pending action redirects to dashboard', function () {
    $brand = ($this->makeUser)('brand-normal@example.com', 'brand');

    $response = $this->post('/login', [
        'email' => $brand->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard.index', absolute: false));
    expect(CartItem::count())->toBe(0);
});
