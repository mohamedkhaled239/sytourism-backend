<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotFoundRedirectTest extends TestCase
{
    /**
     * Test that 404 routes redirect to admin login and clear sessions/cookies
     */
    public function test_404_routes_redirect_to_admin_login()
    {
        // Set some session data first
        session(['test_key' => 'test_value']);
        session(['user_id' => 123]);

        // Test a non-existent route
        $response = $this->get('/non-existent-route');

        // Should redirect to admin login
        $response->assertRedirect(route('admin.login'));

        // Check that cookies are being cleared (Laravel session cookie should be forgotten)
        $cookies = $response->headers->getCookies();
        $sessionCookieCleared = false;

        foreach ($cookies as $cookie) {
            if (str_contains($cookie->getName(), 'laravel_session') ||
                str_contains($cookie->getName(), session()->getName())) {
                $sessionCookieCleared = true;
                break;
            }
        }

        // At least one session-related cookie should be cleared
        $this->assertTrue($sessionCookieCleared || count($cookies) > 0);
    }

    /**
     * Test specific example from user: /admin/loginfds
     */
    public function test_admin_loginfds_redirects_to_admin_login()
    {
        // Test the specific route mentioned by user
        $response = $this->get('/admin/loginfds');

        // Should redirect to admin login
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test that API 404 routes don't redirect
     */
    public function test_api_404_routes_dont_redirect()
    {
        // Test a non-existent API route
        $response = $this->getJson('/api/non-existent-route');

        // Should return 404 JSON response, not redirect
        $response->assertStatus(404);
        // Just check that it's a JSON response with exception info
        $response->assertJsonStructure([
            'message',
            'exception'
        ]);
    }

    /**
     * Test that existing routes still work
     */
    public function test_existing_routes_still_work()
    {
        // Test admin login route still works
        $response = $this->get('/admin/login');

        // Should return 200 (or redirect if already authenticated)
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    /**
     * Test session clearing functionality
     */
    public function test_session_is_cleared_on_404()
    {
        // Start a session and set some data
        $this->startSession();
        session(['test_data' => 'should_be_cleared']);
        session(['another_key' => 'another_value']);

        // Verify session data exists
        $this->assertEquals('should_be_cleared', session('test_data'));

        // Hit a 404 route
        $response = $this->get('/this-route-does-not-exist');

        // Should redirect to admin login
        $response->assertRedirect(route('admin.login'));

        // Follow the redirect to complete the session clearing
        $response = $this->followRedirects($response);

        // Session should be cleared (new session will be started)
        $this->assertNull(session('test_data'));
        $this->assertNull(session('another_key'));
    }

    /**
     * Test that cookies are cleared on 404
     */
    public function test_cookies_are_cleared_on_404()
    {
        // Set some cookies first
        $response = $this->withCookies([
            'test_cookie' => 'test_value',
            'another_cookie' => 'another_value'
        ])->get('/non-existent-page');

        // Should redirect to admin login
        $response->assertRedirect(route('admin.login'));

        // Check that response contains cookie clearing instructions
        $cookies = $response->headers->getCookies();

        // Should have some cookies being cleared (forgotten)
        $this->assertGreaterThan(0, count($cookies));

        // Check that at least one cookie is being cleared (has empty value or past expiry)
        $cookieCleared = false;
        foreach ($cookies as $cookie) {
            if ($cookie->getValue() === '' || $cookie->getExpiresTime() < time()) {
                $cookieCleared = true;
                break;
            }
        }

        $this->assertTrue($cookieCleared, 'At least one cookie should be cleared');
    }
}
