<?php

namespace App\Tests\Service;

use App\Service\SeoMetadataService;
use PHPUnit\Framework\TestCase;

class SeoMetadataServiceTest extends TestCase
{
    private SeoMetadataService $service;

    protected function setUp(): void
    {
        $this->service = new SeoMetadataService();
    }

    public function testServiceExists(): void
    {
        $this->assertInstanceOf(SeoMetadataService::class, $this->service);
    }

    public function testGetMetadataForHome(): void
    {
        $metadata = $this->service->getMetadata('home');

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('title', $metadata);
        $this->assertArrayHasKey('description', $metadata);
        $this->assertArrayHasKey('keywords', $metadata);
    }

    public function testGetMetadataForImmigration(): void
    {
        $metadata = $this->service->getMetadata('immigration');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Immigration', $metadata['title']);
        $this->assertStringContainsString('immigration', strtolower($metadata['description']));
    }

    public function testGetMetadataForTravail(): void
    {
        $metadata = $this->service->getMetadata('travail');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Emploi', $metadata['title']);
    }

    public function testGetMetadataForEtude(): void
    {
        $metadata = $this->service->getMetadata('etude');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Études', $metadata['title']);
    }

    public function testGetMetadataForSponsor(): void
    {
        $metadata = $this->service->getMetadata('sponsor');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Parrainage', $metadata['title']);
    }

    public function testGetMetadataForApropos(): void
    {
        $metadata = $this->service->getMetadata('a_propos');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Propos', $metadata['title']);
    }

    public function testGetMetadataForContact(): void
    {
        $metadata = $this->service->getMetadata('contact');

        $this->assertIsArray($metadata);
        $this->assertStringContainsString('Contact', $metadata['title']);
    }

    public function testGetMetadataDefaultsToHome(): void
    {
        $metadata = $this->service->getMetadata('nonexistent');

        // Should return home metadata as default
        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('title', $metadata);
    }

    public function testGetField(): void
    {
        $title = $this->service->getField('home', 'title');

        $this->assertIsString($title);
        $this->assertNotEmpty($title);
        $this->assertStringContainsString('Canadaim', $title);
    }

    public function testGetAllMetadata(): void
    {
        $allMetadata = $this->service->getAllMetadata();

        $this->assertIsArray($allMetadata);
        $this->assertGreaterThan(0, count($allMetadata));
        
        // Check that all pages have required fields
        foreach ($allMetadata as $page => $metadata) {
            $this->assertArrayHasKey('title', $metadata);
            $this->assertArrayHasKey('description', $metadata);
            $this->assertArrayHasKey('keywords', $metadata);
        }
    }

    public function testMetadataContainsOgImage(): void
    {
        $pages = ['home', 'immigration', 'travail', 'etude', 'sponsor', 'a_propos', 'contact'];

        foreach ($pages as $page) {
            $metadata = $this->service->getMetadata($page);
            $this->assertArrayHasKey('og_image', $metadata);
        }
    }

    public function testAllMetadataFieldsAreNotEmpty(): void
    {
        $allMetadata = $this->service->getAllMetadata();

        foreach ($allMetadata as $page => $metadata) {
            $this->assertNotEmpty($metadata['title'], "Title is empty for page: $page");
            $this->assertNotEmpty($metadata['description'], "Description is empty for page: $page");
            $this->assertNotEmpty($metadata['keywords'], "Keywords are empty for page: $page");
        }
    }

    public function testMetadataKeywordsCoverage(): void
    {
        $allMetadata = $this->service->getAllMetadata();

        // Each page should have meaningful keywords
        foreach ($allMetadata as $page => $metadata) {
            $keywords = $metadata['keywords'];
            $this->assertStringContainsString('Canada', $keywords);
        }
    }
}
