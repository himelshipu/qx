<?php

declare(strict_types=1);

use App\Models\Influencer;
use App\Models\InfluencerPlatformStat;
use App\Models\Package;
use App\Models\User;

it('matches influencers by platform content type keywords and follower range', function (): void {
    $youtubeCreator = User::create([
        'name' => 'YouTube Creator',
        'email' => 'youtube-creator@example.com',
        'password' => 'password',
        'user_type' => 'influencer',
        'is_active' => true,
    ]);

    $youtubeInfluencer = Influencer::create([
        'user_id' => $youtubeCreator->id,
        'display_name' => 'YouTube Creator',
        'is_active' => true,
    ]);

    InfluencerPlatformStat::create([
        'influencer_id' => $youtubeInfluencer->id,
        'platform' => 'youtube',
        'handle' => 'ytcreator',
        'follower_count' => 25000,
        'is_active' => true,
    ]);

    Package::create([
        'influencer_id' => $youtubeInfluencer->id,
        'platform' => 'youtube',
        'name' => '1 YouTube Short (30 Seconds)',
        'description' => 'Short-form video package.',
        'base_price' => 150,
        'currency' => 'USD',
        'delivery_days' => 7,
        'revisions_included' => 1,
        'is_active' => true,
    ]);

    $videoCreator = User::create([
        'name' => 'YouTube Video Creator',
        'email' => 'youtube-video@example.com',
        'password' => 'password',
        'user_type' => 'influencer',
        'is_active' => true,
    ]);

    $videoInfluencer = Influencer::create([
        'user_id' => $videoCreator->id,
        'display_name' => 'YouTube Video Creator',
        'is_active' => true,
    ]);

    InfluencerPlatformStat::create([
        'influencer_id' => $videoInfluencer->id,
        'platform' => 'youtube',
        'handle' => 'ytvideo',
        'follower_count' => 25000,
        'is_active' => true,
    ]);

    Package::create([
        'influencer_id' => $videoInfluencer->id,
        'platform' => 'youtube',
        'name' => '1 YouTube Video',
        'description' => 'Long-form video package.',
        'base_price' => 500,
        'currency' => 'USD',
        'delivery_days' => 8,
        'revisions_included' => 1,
        'is_active' => true,
    ]);

    $response = $this->get(route('influencers.platform', ['platformSlug' => 'youtube']) . '?contentTypes=shorts&followers=10001-50000');

    $response->assertOk();
    $response->assertSee('YouTube Creator');
    $response->assertDontSee('YouTube Video Creator');
});

it('disables dependent filters until a platform is selected', function (): void {
    $response = $this->get(route('influencers'));

    $response->assertOk();
    $response->assertSeeHtml('id="content-type-trigger" type="button" disabled');
    $response->assertSeeHtml('id="followers-trigger" type="button" disabled');
    $response->assertSee('Select a platform first');
});
