<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_each_dashboard_sidebar_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        foreach (['dashboard', 'transactions', 'reports', 'users', 'rates', 'areas', 'vehicles', 'logs'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }
}