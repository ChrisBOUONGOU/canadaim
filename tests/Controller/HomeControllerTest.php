<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testHomepageContainsExpectedContent(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertStringContainsString('Canadaim', $client->getResponse()->getContent());
    }

    public function testHomepageHasProperTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(1, $crawler->filter('title'));
        $this->assertStringContainsString('Canadaim', $crawler->filter('title')->text());
    }

    public function testHomepageHasMetaTags(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $metas = $crawler->filter('meta');
        $this->assertGreaterThan(0, $metas->count());
    }

    public function testHomepageHasNavigation(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Check for navigation links
        $navLinks = $crawler->filter('a');
        $this->assertGreaterThan(0, $navLinks->count());
    }

    public function testHomepageHasHeaderElement(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $header = $crawler->filter('header');
        $this->assertCount(1, $header);
    }

    public function testHomepageHasFooterElement(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $footer = $crawler->filter('footer');
        $this->assertCount(1, $footer);
    }

    public function testResponseHasSecurityHeaders(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $response = $client->getResponse();
        
        // Check for security headers
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertTrue($response->headers->has('X-Content-Type-Options'));
    }

    public function testResponseHasOpenGraphTags(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $ogTags = $crawler->filter('meta[property^="og:"]');
        $this->assertGreaterThan(0, $ogTags->count());
    }
}
