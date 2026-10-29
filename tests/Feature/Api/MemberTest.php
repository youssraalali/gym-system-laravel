<?php

namespace Tests\Feature\Api;

use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(
            [
                'role' => 'admin',
            ]
        );
        Sanctum::actingAs($user, ['*']);

    }

    public function test_admin_can_list_members(): void
    {
        Member::factory()->count(3)->create();

        $response = $this->getJson('/api/members');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_member(): void
    {
        $plan = MembershipPlan::factory()->create();
        $membership_plan_id = $plan->id;
        $payload = [
            'full_name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone_number' => '1234567890',
            'membership_start_date' => '2023-01-01',
            'membership_plan_id' => $membership_plan_id,
        ];

        $response = $this->postJson('/api/members', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('members', [
            'email' => 'john.doe@example.com',
            'membership_plan_id' => $plan->id,
        ]);
    }

    public function test_admin_cannot_create_member_with_missing_fields(): void
    {
        $response = $this->postJson('/api/members', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['full_name', 'email', 'phone_number', 'membership_start_date', 'membership_plan_id']);
    }

    public function test_admin_cannot_create_member_with_duplicate_email(): void
    {
        $existing = Member::factory()->create();
        $payload = [
            'full_name' => 'Jane Doe',
            'email' => $existing->email,
            'phone_number' => '0987654321',
            'membership_start_date' => '2023-02-01',
            'membership_plan_id' => $existing->membership_plan_id,
        ];
        $response = $this->postJson('/api/members', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_admin_cannot_create_member_with_nonexistent_plan(): void
    {
        $payload = [
            'full_name' => 'Alice Smith',
            'email' => 'alice.smith@example.com',
            'phone_number' => '5551234567',
            'membership_start_date' => '2023-03-01',
            'membership_plan_id' => 9999,
        ];
        $response = $this->postJson('/api/members', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['membership_plan_id'])
            ->assertJsonMissingValidationErrors(['email', 'full_name']);
    }

    public function test_admin_can_view_a_member(): void
    {
        $member = Member::factory()->create();
        $response = $this->getJson("/api/members/{$member->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $member->id)
            ->assertJsonPath('data.email', $member->email)
            ->assertJsonPath('data.membership_name', $member->membershipPlan->name);

    }

    public function test_admin_gets_404_for_nonexistent_member(): void
    {
        $response = $this->getJson('/api/members/9999');
        $response->assertStatus(404);
    }

    public function test_admin_can_update_member(): void
    {
        $member = Member::factory()->create();
        $payload = [
            'full_name' => 'Updated Name',
            'email' => 'updated.email@example.com',
            'phone_number' => '9876543210',
            'status' => true,
            'membership_start_date' => '2023-04-01',
            'membership_plan_id' => $member->membership_plan_id,
        ];

        $response = $this->putJson("/api/members/{$member->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonPath('member.full_name', 'Updated Name')
            ->assertJsonPath('message', 'Member updated successfully');

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'full_name' => 'Updated Name',
            'email' => 'updated.email@example.com',
        ]);
    }

    public function test_admin_cannot_update_member_with_invalid_data(): void
    {

        $member = Member::factory()->create();
        $payload = [
            'full_name' => 'john doe',
            'email' => 'not-an-email',
            'phone_number' => '4567890123',
            'status' => true,
            'membership_start_date' => '2023-05-01',
            'membership_plan_id' => $member->membership_plan_id,
        ];
        $response = $this->putJson("/api/members/{$member->id}", $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_admin_can_update_member_keeping_same_email(): void
    {
        $member = Member::factory()->create();

        $payload = [
            'full_name' => 'Updated Name',
            'email' => $member->email,
            'phone_number' => '9876543210',
            'status' => true,
            'membership_start_date' => '2023-04-01',
            'membership_plan_id' => $member->membership_plan_id,
        ];

        $response = $this->putJson("/api/members/{$member->id}", $payload);

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_member(): void
    {
        $member = Member::factory()->create();
        $response = $this->deleteJson("/api/members/{$member->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    public function test_search_returns_only_members_matching_the_term(): void
    {
        Member::factory()->create(['full_name' => 'zed', 'email' => 'zed@example.com']);
        Member::factory()->create(['full_name' => 'mia', 'email' => 'mia@example.com']);
        Member::factory()->create(['full_name' => 'omar', 'email' => 'omr@example.com']);

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);
        $response = $this->getJson('/api/members?search=zed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'zed');
    }

    public function test_search_returns_only_members_matching_the_email(): void
    {
        Member::factory()->create(['full_name' => 'omar', 'email' => 'zed.fan@example.com']);
        Member::factory()->create(['full_name' => 'mia', 'email' => 'mi@example.com']);

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);
        $response = $this->getJson('/api/members?search=zed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'omar');
    }

    public function test_plan_filter_returns_only_members_with_the_specified_plan(): void
    {

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $monthlyPlan = MembershipPlan::factory()->create(['name' => 'Monthly']);
        $yearlyPlan = MembershipPlan::factory()->create(['name' => 'Yearly']);
        Member::factory()->create(['full_name' => 'zed', 'membership_plan_id' => $monthlyPlan->id]);
        Member::factory()->create(['full_name' => 'mia', 'membership_plan_id' => $yearlyPlan->id]);

        $response = $this->getJson('/api/members?plan=Monthly');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'zed')
            ->assertJsonMissingExact(['full_name' => 'mia']);

    }

    public function test_page_2_returns_the_correct_members(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $plan = MembershipPlan::factory()->create();
        Member::factory()->count(15)->create(['membership_plan_id' => $plan->id]);

        $response = $this->getJson('/api/members?page=2');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_sorting_by_full_name_returns_members_in_ascending_order(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $plan = MembershipPlan::factory()->create();
        Member::factory()->create(['full_name' => 'zed', 'membership_plan_id' => $plan->id]);
        Member::factory()->create(['full_name' => 'amir', 'membership_plan_id' => $plan->id]);
        Member::factory()->create(['full_name' => 'yas', 'membership_plan_id' => $plan->id]);

        $response = $this->getJson('/api/members?sort=full_name');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.full_name', 'amir')
            ->assertJsonPath('data.1.full_name', 'yas')
            ->assertJsonPath('data.2.full_name', 'zed');
    }

    public function test_sorting_by_full_name_returns_members_in_descending_order(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $plan = MembershipPlan::factory()->create();
        Member::factory()->create(['full_name' => 'zed', 'membership_plan_id' => $plan->id]);
        Member::factory()->create(['full_name' => 'yas', 'membership_plan_id' => $plan->id]);
        Member::factory()->create(['full_name' => 'amir', 'membership_plan_id' => $plan->id]);

        $response = $this->getJson('/api/members?sort=-full_name');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.full_name', 'zed')
            ->assertJsonPath('data.1.full_name', 'yas')
            ->assertJsonPath('data.2.full_name', 'amir');
    }

    public function test_invalid_sort_parameter_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);
        $response = $this->getJson('/api/members?sort=invalid_column');
        $response->assertStatus(422);
    }

    public function test_search_and_plan_combined_filters_return_correct_members(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $monthlyPlan = MembershipPlan::factory()->create(['name' => 'Monthly']);
        $yearlyPlan = MembershipPlan::factory()->create(['name' => 'Yearly']);

        Member::factory()->create(['full_name' => 'zed', 'membership_plan_id' => $monthlyPlan->id, 'status' => true]);
        Member::factory()->create(['full_name' => 'zed', 'email' => 'zed@example.com', 'membership_plan_id' => $yearlyPlan->id, 'status' => true]);
        Member::factory()->create(['full_name' => 'omar', 'membership_plan_id' => $monthlyPlan->id, 'status' => true]);

        $response = $this->getJson('/api/members?search=zed&plan=Monthly');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.full_name', 'zed')
            ->assertJsonMissingExact(['full_name' => 'zed', 'membership_plan_id' => $yearlyPlan->id])
            ->assertJsonMissingExact(['full_name' => 'omar']);
    }

    public function test_query_string_parameters_are_preserved_in_pagination_links(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['*']);

        $plan = MembershipPlan::factory()->create(['name' => 'Monthly']);
        Member::factory()->count(15)->create(['full_name' => 'zed', 'membership_plan_id' => $plan->id, 'status' => true]);

        $response = $this->getJson('/api/members?search=zed&plan=Monthly&page=1');

        $response->assertStatus(200)
            ->assertJsonPath('links.next', url('/api/members?search=zed&plan=Monthly&page=2'));
    }
}
