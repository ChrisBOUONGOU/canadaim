<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testAdminDashboardIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testAdminDashboardHasTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $this->assertStringContainsString('Admin', $client->getResponse()->getContent());
    }

    public function testAdminMessagesPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/messages');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminRequestsPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/requests');

        $this->assertResponseIsSuccessful();
    }

    public function testAdminMessagesPageContainsTable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/messages');

        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count());
    }

    public function testAdminRequestsPageContainsTable(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/requests');

        $table = $crawler->filter('table');
        $this->assertGreaterThan(0, $table->count());
    }

    public function testAdminDashboardShowsStatistics(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $content = $client->getResponse()->getContent();
        
        // Should display some statistics/counts
        $this->assertStringContainsString('Message', $content);
    }

    public function testAdminPageHasNavigation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');

        $links = $crawler->filter('a');
        $this->assertGreaterThan(0, $links->count());
    }

    public function testAdminHeadersAreSet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        $response = $client->getResponse();
        
        // Check for security headers
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertTrue($response->headers->has('X-Content-Type-Options'));
    }
}
