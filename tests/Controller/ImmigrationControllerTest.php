<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImmigrationControllerTest extends WebTestCase
{
    public function testImmigrationPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/immigration');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testImmigrationPageTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/immigration');

        $title = $crawler->filter('title')->text();
        $this->assertStringContainsString('Immigration', $title);
    }

    public function testImmigrationPageHasForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/immigration');

        $form = $crawler->filter('form');
        $this->assertGreaterThan(0, $form->count());
    }

    public function testImmigrationPageMetaDescription(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/immigration');

        $description = $crawler->filter('meta[name="description"]');
        $this->assertCount(1, $description);
        $this->assertStringContainsString('immigration', strtolower($description->attr('content')));
    }

    public function testImmigrationFormCanBeSubmitted(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/immigration');

        $form = $crawler->selectButton('Envoyer')->form();

        // Try to submit form with valid data
        $form['service_request[type]'] = 'immigration';
        $form['service_request[name]'] = 'Test User';
        $form['service_request[email]'] = 'test@example.com';
        $form['service_request[phone]'] = '+1-581-222-5712';
        $form['service_request[country]'] = 'France';
        $form['service_request[details]'] = 'I need immigration help for Canada';

        $client->submit($form);

        // Should either redirect or show success message
        $this->assertTrue(
            $client->getResponse()->isRedirect() || 
            $client->getResponse()->isSuccessful()
        );
    }
}
