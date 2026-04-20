<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\CampaignInfluencer;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\SubOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignInfluencerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $brand;
    protected Brand $brandModel;
    protected Campaign $campaign;
    protected Influencer $influencer1;
    protected Influencer $influencer2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['user_type' => 'admin']);
        $this->brand = User::factory()->create(['user_type' => 'brand']);

        // Create brand
        $this->brandModel = Brand::factory()->create();

        // Create campaign
        $this->campaign = Campaign::factory()->create([
            'brand_id'   => $this->brandModel->id,
            'created_by' => $this->admin->id,
            'status'     => 'published'
        ]);

        // Create influencers
        $this->influencer1 = Influencer::factory()->create();
        $this->influencer2 = Influencer::factory()->create();
    }

    /**
     * Test Workflow A Step 1: Create Campaign
     */
    public function test_campaign_created_successfully(): void
    {
        $this->assertDatabaseHas('campaigns', [
            'id'       => $this->campaign->id,
            'brand_id' => $this->brandModel->id,
            'status'   => 'published'
        ]);
    }

    /**
     * Test Workflow A Step 2: Assign Influencers to Campaign
     */
    public function test_assign_influencers_to_campaign(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('dashboard.campaigns.influencers.store', $this->campaign), [
            'influencer_ids' => [$this->influencer1->id, $this->influencer2->id]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('campaign_influencers', [
            'campaign_id' => $this->campaign->id,
            'influencer_id'  => $this->influencer1->id,
            'status'      => 'assigned'
        ]);

        $this->assertDatabaseHas('campaign_influencers', [
            'campaign_id' => $this->campaign->id,
            'influencer_id'  => $this->influencer2->id,
            'status'      => 'assigned'
        ]);
    }

    /**
     * Test Workflow A Step 3: Approve/Reject Influencers
     */
    public function test_approve_influencer_assignment(): void
    {
        $this->actingAs($this->admin);

        // Create assignment
        $assignment = CampaignInfluencer::factory()->create([
            'campaign_id' => $this->campaign->id,
            'influencer_id'  => $this->influencer1->id,
            'status'      => 'assigned'
        ]);

        // Approve
        $response = $this->post(route('campaign-influencers.approve', $assignment));
        $response->assertRedirect();

        $this->assertDatabaseHas('campaign_influencers', [
            'id'          => $assignment->id,
            'status'      => 'approved',
            'approved_by' => $this->admin->id
        ]);
    }

    /**
     * Test Workflow A Step 4: Create Master Order with Sub-Orders
     */
    public function test_create_master_order_with_sub_orders(): void
    {
        $this->actingAs($this->admin);

        // Create and approve influencer assignments
        $assignment1 = CampaignInfluencer::factory()->create([
            'campaign_id' => $this->campaign->id,
            'influencer_id'  => $this->influencer1->id,
            'status'      => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now()
        ]);

        // Create master order
        $response = $this->post(route('dashboard.orders.create-from-campaign'), [
            'campaign_id' => $this->campaign->id,
            'brand_id'    => $this->brandModel->id
        ]);

        $response->assertRedirect();

        // Check master order created
        $order = Order::where('campaign_id', $this->campaign->id)->first();
        $this->assertNotNull($order);

        // Check sub-order created
        $subOrder = SubOrder::where('order_id', $order->id)->first();
        $this->assertNotNull($subOrder);
        $this->assertEquals('pending', $subOrder->status);
    }

    /**
     * Test Workflow A Step 5: Update Sub-Order Status
     */
    public function test_update_sub_order_status(): void
    {
        $this->actingAs($this->admin);

        // Create order and sub-order
        $order = Order::factory()->create([
            'campaign_id' => $this->campaign->id,
            'brand_id'    => $this->brandModel->id
        ]);

        $subOrder = SubOrder::factory()->create([
            'order_id'   => $order->id,
            'influencer_id' => $this->influencer1->id,
            'status'     => 'pending'
        ]);

        // Update status to in_progress
        $response = $this->put(route('sub-orders.update-status', $subOrder), [
            'status' => 'in_progress'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('sub_orders', [
            'id'     => $subOrder->id,
            'status' => 'in_progress'
        ]);
    }

    /**
     * Test Workflow A Step 6: Mark Sub-Order as Paid
     */
    public function test_mark_sub_order_paid(): void
    {
        $this->actingAs($this->admin);

        $order = Order::factory()->create([
            'campaign_id' => $this->campaign->id
        ]);

        $subOrder = SubOrder::factory()->create([
            'order_id'   => $order->id,
            'influencer_id' => $this->influencer1->id,
            'status'     => 'completed',
            'paid_at'    => null
        ]);

        $response = $this->post(route('sub-orders.mark-paid', $subOrder));
        $response->assertRedirect();

        $this->assertDatabaseHas('sub_orders', [
            'id' => $subOrder->id
        ]);

        // Check paid_at is set
        $updated = SubOrder::find($subOrder->id);
        $this->assertNotNull($updated->paid_at);
    }

    /**
     * Test that influencer can have campaign assignment
     */
    public function test_influencer_has_campaign_assignments(): void
    {
        $assignment = CampaignInfluencer::factory()->create([
            'campaign_id' => $this->campaign->id,
            'influencer_id'  => $this->influencer1->id
        ]);

        $this->influencer1->refresh();
        $assignments = $this->influencer1->campaignAssignments;

        $this->assertCount(1, $assignments);
        $this->assertEquals($assignment->id, $assignments->first()->id);
    }

    /**
     * Test that order has sub-orders
     */
    public function test_order_has_sub_orders(): void
    {
        $order = Order::factory()->create([
            'campaign_id' => $this->campaign->id
        ]);

        $subOrder1 = SubOrder::factory()->create(['order_id' => $order->id]);
        $subOrder2 = SubOrder::factory()->create(['order_id' => $order->id]);

        $this->assertTrue($order->isMasterOrder());
        $this->assertCount(2, $order->subOrders);
    }
}
