<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testContactPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testContactPageTitle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $title = $crawler->filter('title')->text();
        $this->assertStringContainsString('Contact', $title);
    }

    public function testContactPageHasForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $forms = $crawler->filter('form');
        $this->assertGreaterThan(0, $forms->count());
    }

    public function testContactFormHasRequiredFields(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        // Check for form fields
        $this->assertGreaterThan(0, $crawler->filter('input[name*="name"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('input[name*="email"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('textarea[name*="message"]')->count());
    }

    public function testContactPageDisplaysContactInfo(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $content = $client->getResponse()->getContent();
        
        // Check for phone number
        $this->assertStringContainsString('581-222-5712', $content);
    }

    public function testContactPageHasMultipleSections(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        // Should have multiple cards/sections
        $cards = $crawler->filter('.card, [class*="card"]');
        $this->assertGreaterThan(0, $cards->count());
    }

    public function testContactFormCanBeSubmitted(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $form = $crawler->selectButton('Envoyer')->form();

        // Fill in form with valid data
        $form['contact_message[name]'] = 'Jean Dupont';
        $form['contact_message[email]'] = 'jean@example.com';
        $form['contact_message[phone]'] = '+1-581-222-5712';
        $form['contact_message[subject]'] = 'immigration';
        $form['contact_message[message]'] = 'I am very interested in immigrating to Canada and would like to discuss options with you.';

        $client->submit($form);

        // Should redirect after successful submission
        $this->assertTrue($client->getResponse()->isRedirect() || $client->getResponse()->isSuccessful());
    }

    public function testContactPageRespondsCorrectly(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');

        $response = $client->getResponse();
        
        // Check headers
        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }
}
