<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Dusk browser tests for login functionality.
 * 
 * Make sure to seed test data before running: php artisan migrate:fresh --seed --env=dusk.local
 */
class LoginTest extends DuskTestCase
{
    public function test_user_can_see_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertSee('Email')
                    ->assertSee('Password');
        });
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        // Run invalid login test first to avoid session issues
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/login')
                    ->type('email', 'wrong@example.com')
                    ->type('password', 'wrongpassword')
                    ->click('button[type="submit"]')
                    ->pause(1000)
                    ->assertPathIs('/login');
        });
    }

    public function test_user_can_login_with_seeded_user(): void
    {
        // Uses admin@demo.test from TenantSeeder
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/login')
                    ->type('email', 'admin@demo.test')
                    ->type('password', 'password')
                    ->click('button[type="submit"]')
                    ->pause(2000)
                    ->assertPathIsNot('/login');
        });
    }
}
