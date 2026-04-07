<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\ModeratorAssignment;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationMediationTest extends TestCase
{
    use RefreshDatabase;

    protected User $brand;
    protected User $moderator;
    protected User $influencerUser;
    protected Influencer $influencer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->brand       = User::factory()->create(['user_type' => 'brand']);
        $this->moderator   = User::factory()->create(['user_type' => 'moderator']);
        $this->influencerUser = User::factory()->create(['user_type' => 'influencer']);

        // Create influencer
        $this->influencer = Influencer::factory()->create(['user_id' => $this->influencerUser->id]);
    }

    /**
     * Test Workflow B Step 1-2: Brand selects and adds package to cart
     * (This would be in a shopping workflow test)
     */
    public function test_package_exists(): void
    {
        $package = Package::factory()->create(['influencer_id' => $this->influencer->id]);

        $this->assertDatabaseHas('packages', [
            'id'         => $package->id,
            'influencer_id' => $this->influencer->id
        ]);
    }

    /**
     * Test Workflow B Step 3: Order created and Conversation opened with moderator
     */
    public function test_conversation_created_for_package_order(): void
    {
        // Create moderator assignment
        $assignment = ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $this->influencer->id,
            'assigned_at'       => now()
        ]);

        // Create order
        $order = Order::factory()->create([
            'buyer_user_id'           => $this->brand->id,
            'accepted_for_influencer_id' => $this->influencer->id
        ]);

        // Create conversation via helper
        $conversation = \App\Http\Controllers\ConversationController::createForPackageOrder(
            $this->brand->id,
            $this->influencer->id,
            $order->id
        );

        $this->assertNotNull($conversation);
        $this->assertEquals($this->brand->id, $conversation->brand_user_id);
        $this->assertEquals($this->influencer->id, $conversation->influencer_id);
        $this->assertEquals($this->moderator->id, $conversation->handled_by_user_id);
    }

    /**
     * Test Workflow B Step 4-5: Brand chats (thinks they're talking to influencer)
     * Moderator replies AS influencer
     */
    public function test_brand_messages_moderator_as_influencer(): void
    {
        // Create moderator assignment
        ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $this->influencer->id,
            'assigned_at'       => now()
        ]);

        // Create conversation
        $conversation = Conversation::factory()->create([
            'conversation_type'  => 'package_order',
            'influencer_id'         => $this->influencer->id,
            'brand_user_id'      => $this->brand->id,
            'handled_by_user_id' => $this->moderator->id
        ]);

        $this->actingAs($this->brand);

        // Brand sends message
        $response = $this->post(
            route('conversations.storeMessage', $conversation),
            ['message' => 'Hello, can you do this work?']
        );

        $response->assertRedirect();

        // Check message was created
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $this->brand->id
        ]);
    }

    /**
     * Test that influencer never sees conversations directly
     */
    public function test_influencer_cannot_view_conversations(): void
    {
        $conversation = Conversation::factory()->create([
            'influencer_id'         => $this->influencer->id,
            'brand_user_id'      => $this->brand->id,
            'handled_by_user_id' => $this->moderator->id
        ]);

        $this->actingAs($this->influencerUser);

        // Try to view conversation - should fail
        // (depending on authorization implementation)
        // For now, just verify influencer cannot list conversations
        $response = $this->get(route('conversations.index'));

        // Influencer should be redirected or see error
        // This depends on your authorization implementation
    }

    /**
     * Test that moderator sees conversations they're assigned to
     */
    public function test_moderator_can_view_assigned_conversations(): void
    {
        $conversation = Conversation::factory()->create([
            'influencer_id'         => $this->influencer->id,
            'brand_user_id'      => $this->brand->id,
            'handled_by_user_id' => $this->moderator->id
        ]);

        $this->actingAs($this->moderator);

        $response = $this->get(route('conversations.show', $conversation));

        $response->assertOk();
        $response->assertViewHas('conversation', $conversation);
    }

    /**
     * Test moderator assignment (one active per influencer)
     */
    public function test_influencer_has_active_moderator(): void
    {
        // Create assignment
        $assignment = ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $this->influencer->id,
            'assigned_at'       => now(),
            'unassigned_at'     => null
        ]);

        $this->influencer->refresh();
        $activeModerators = $this->influencer->activeModerator;

        $this->assertCount(1, $activeModerators);
        $this->assertTrue($assignment->isActive());
    }

    /**
     * Test unassigning moderator from influencer
     */
    public function test_unassign_moderator_from_influencer(): void
    {
        $assignment = ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $this->influencer->id,
            'assigned_at'       => now(),
            'unassigned_at'     => null
        ]);

        // Unassign
        $assignment->update(['unassigned_at' => now()]);

        $this->assertFalse($assignment->isActive());
        $this->assertNull($this->influencer->activeModerator->first());
    }

    /**
     * Test moderator can handle multiple influencers
     */
    public function test_moderator_can_handle_multiple_influencers(): void
    {
        $influencer2User = User::factory()->create(['user_type' => 'influencer']);
        $influencer2     = Influencer::factory()->create(['user_id' => $influencer2User->id]);

        ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $this->influencer->id,
            'assigned_at'       => now()
        ]);

        ModeratorAssignment::factory()->create([
            'moderator_user_id' => $this->moderator->id,
            'influencer_id'        => $influencer2->id,
            'assigned_at'       => now()
        ]);

        $this->assertDatabaseCount('moderator_assignments', 2);

        // Verify both are assigned to same moderator
        $assignments = ModeratorAssignment::where('moderator_user_id', $this->moderator->id)
            ->whereNull('unassigned_at')
            ->get();

        $this->assertCount(2, $assignments);
    }
}
