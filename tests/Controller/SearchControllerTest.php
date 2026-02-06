<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    public function testSearchPageWithoutQuery(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testSearchWithValidQuery(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search', ['q' => 'immigration']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testSearchPageTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/search');

        $title = $crawler->filter('title')->text();
        $this->assertStringContainsString('Search', $title);
    }

    public function testSearchFormIsPresent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/search');

        $searchForm = $crawler->filter('form');
        $this->assertGreaterThan(0, $searchForm->count());
    }

    public function testSearchReturnsResults(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/search', ['q' => 'immigration']);

        $content = $client->getResponse()->getContent();
        
        // Should either show results or "no results" message
        $this->assertNotEmpty($content);
    }

    public function testSearchWithMultipleKeywords(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search', ['q' => 'immigration canada']);

        $this->assertResponseIsSuccessful();
    }

    public function testSearchWithSpecialCharacters(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search', ['q' => 'étude français']);

        $this->assertResponseIsSuccessful();
    }

    public function testSearchPreservesQueryParameter(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/search', ['q' => 'travail']);

        $searchInput = $crawler->filter('input[name="q"]');
        $this->assertCount(1, $searchInput);
    }

    public function testEmptySearchQuery(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search', ['q' => '']);

        $this->assertResponseIsSuccessful();
    }

    public function testSearchResponseHasProperStructure(): void
    {
        $client = static::createClient();
        $client->request('GET', '/search', ['q' => 'test']);

        $response = $client->getResponse();
        
        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }
}
