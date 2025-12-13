<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Dusk browser tests for ticket functionality.
 * 
 * Make sure to seed test data before running: php artisan migrate:fresh --seed --env=dusk.local
 */
class TicketTest extends DuskTestCase
{
    protected string $tenantSlug = 'demo'; // From TenantSeeder

    public function test_user_can_view_ticket_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::first())
                    ->visit("/t/{$this->tenantSlug}/tickets")
                    ->pause(500)
                    ->assertPresent('h1');
        });
    }

    public function test_user_can_view_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::first())
                    ->visit('/dashboard')
                    ->assertSee('Dashboard');
        });
    }
}
