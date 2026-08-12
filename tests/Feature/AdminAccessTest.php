<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);
    }

    private function regularUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_admin_can_access_clients(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('clients.index'))
            ->assertOk();
    }

    public function test_non_admin_receives_forbidden_on_dashboard(): void
    {
        $this->actingAs($this->regularUser())
            ->get(route('dashboard'))
            ->assertForbidden();
    }

    public function test_non_admin_receives_forbidden_on_clients(): void
    {
        $this->actingAs($this->regularUser())
            ->get(route('clients.index'))
            ->assertForbidden();
    }

    public function test_non_admin_receives_forbidden_on_imports(): void
    {
        $this->actingAs($this->regularUser())
            ->get(route('imports.index'))
            ->assertForbidden();
    }

    public function test_profile_remains_accessible_to_non_admin(): void
    {
        $this->actingAs($this->regularUser())
            ->get(route('profile.edit'))
            ->assertOk();
    }
}
