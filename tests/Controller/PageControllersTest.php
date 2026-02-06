<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllersTest extends WebTestCase
{
    /**
     * @dataProvider pageProvider
     */
    public function testPageIsSuccessful(string $route): void
    {
        $client = static::createClient();
        $client->request('GET', $route);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageHasProperTitle(string $route): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $route);

        $title = $crawler->filter('title');
        $this->assertCount(1, $title);
        $this->assertNotEmpty($title->text());
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageHasMetaDescription(string $route): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $route);

        $description = $crawler->filter('meta[name="description"]');
        $this->assertCount(1, $description);
        $this->assertNotEmpty($description->attr('content'));
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageHasSecurityHeaders(string $route): void
    {
        $client = static::createClient();
        $client->request('GET', $route);

        $response = $client->getResponse();
        
        $this->assertTrue($response->headers->has('X-Frame-Options'));
        $this->assertEquals('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageHasOpenGraphTags(string $route): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $route);

        $ogTitle = $crawler->filter('meta[property="og:title"]');
        $this->assertCount(1, $ogTitle);
        
        $ogDescription = $crawler->filter('meta[property="og:description"]');
        $this->assertCount(1, $ogDescription);
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPageHasStructuredData(string $route): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $route);

        $script = $crawler->filter('script[type="application/ld+json"]');
        $this->assertGreaterThan(0, $script->count());
    }

    public function pageProvider(): array
    {
        return [
            ['/'],
            ['/immigration'],
            ['/travail'],
            ['/etude'],
            ['/sponsor'],
            ['/a-propos'],
            ['/contact'],
        ];
    }
}
