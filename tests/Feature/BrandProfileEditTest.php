<?php

use App\Models\Brand;
use App\Models\BrandSocialLink;
use App\Models\User;

test('brand edit page is only accessible for the authenticated user slug', function () {
    $owner = User::factory()->create(['user_type' => 'brand']);
    Brand::query()->create([
        'user_id' => $owner->id,
        'brand_name' => 'Owner Brand',
    ]);

    $other = User::factory()->create(['user_type' => 'brand']);

    $this->actingAs($owner)
        ->get(route('dashboard.brand.profile.edit', ['slug' => $other->slug]))
        ->assertNotFound();
});

test('social tab update does not require brand_name and updates social links', function () {
    $user = User::factory()->create(['user_type' => 'brand']);
    $brand = Brand::query()->create([
        'user_id' => $user->id,
        'brand_name' => 'Acme Co',
    ]);

    $response = $this->actingAs($user)
        ->from(route('dashboard.brand.profile.edit', ['slug' => $user->slug]))
        ->post(route('dashboard.brand.profile.update', ['slug' => $user->slug]), [
            'active_tab' => 'social',
            'website' => 'https://acme.example',
            'instagram' => 'https://instagram.com/acme',
            'tiktok' => 'https://tiktok.com/@acme',
            'facebook' => 'https://facebook.com/acme',
            'twitter' => 'https://x.com/acme',
            'youtube' => 'https://youtube.com/@acme',
            'others' => 'https://acme.example/link',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard.brand.profile.edit', ['slug' => $user->slug]));

    expect($brand->fresh()->website)->toBe('https://acme.example');

    $social = BrandSocialLink::query()->where('brand_id', $brand->id)->first();
    expect($social)->not->toBeNull();
    expect($social->instagram_url)->toBe('https://instagram.com/acme');
    expect($social->x_url)->toBe('https://x.com/acme');
});

test('details tab requires brand_name', function () {
    $user = User::factory()->create(['user_type' => 'brand']);
    Brand::query()->create([
        'user_id' => $user->id,
        'brand_name' => 'Initial Name',
    ]);

    $response = $this->actingAs($user)
        ->from(route('dashboard.brand.profile.edit', ['slug' => $user->slug]))
        ->post(route('dashboard.brand.profile.update', ['slug' => $user->slug]), [
            'active_tab' => 'details',
            'brand_name' => '',
            'location' => 'Dhaka',
        ]);

    $response
        ->assertRedirect(route('dashboard.brand.profile.edit', ['slug' => $user->slug]))
        ->assertSessionHasErrors(['brand_name']);
});

