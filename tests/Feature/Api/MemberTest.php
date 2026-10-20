<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\Member;
use App\Models\MembershipPlan;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(
            [
                'role' => 'admin'
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
            'membership_plan_id' => $membership_plan_id
        ];

        $response = $this->postJson('/api/members', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('members', [
            'email' => 'john.doe@example.com',
            'membership_plan_id' => $plan->id
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
            'membership_plan_id' => $existing->membership_plan_id
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
            'membership_plan_id' => 9999
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
            'membership_plan_id' => $member->membership_plan_id
        ];

        $response = $this->putJson("/api/members/{$member->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonPath('member.full_name', 'Updated Name')
            ->assertJsonPath('message', 'Member updated successfully');


        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'full_name' => 'Updated Name',
            'email' => 'updated.email@example.com'
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
            'membership_plan_id' => $member->membership_plan_id
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
            'id' => $member->id
        ]);
    }

}
