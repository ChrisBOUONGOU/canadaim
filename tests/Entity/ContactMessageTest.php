<?php

namespace App\Tests\Entity;

use App\Entity\ContactMessage;
use PHPUnit\Framework\TestCase;

class ContactMessageTest extends TestCase
{
    private ContactMessage $contactMessage;

    protected function setUp(): void
    {
        $this->contactMessage = new ContactMessage();
    }

    public function testEntityCreation(): void
    {
        $this->assertInstanceOf(ContactMessage::class, $this->contactMessage);
    }

    public function testSetAndGetName(): void
    {
        $name = 'Jean Dupont';
        $this->contactMessage->setName($name);
        $this->assertEquals($name, $this->contactMessage->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'jean@example.com';
        $this->contactMessage->setEmail($email);
        $this->assertEquals($email, $this->contactMessage->getEmail());
    }

    public function testSetAndGetPhone(): void
    {
        $phone = '+1-581-222-5712';
        $this->contactMessage->setPhone($phone);
        $this->assertEquals($phone, $this->contactMessage->getPhone());
    }

    public function testSetAndGetSubject(): void
    {
        $subject = 'immigration';
        $this->contactMessage->setSubject($subject);
        $this->assertEquals($subject, $this->contactMessage->getSubject());
    }

    public function testSetAndGetMessage(): void
    {
        $message = 'I want to immigrate to Canada';
        $this->contactMessage->setMessage($message);
        $this->assertEquals($message, $this->contactMessage->getMessage());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 'new';
        $this->contactMessage->setStatus($status);
        $this->assertEquals($status, $this->contactMessage->getStatus());
    }

    public function testGetCreatedAt(): void
    {
        // createdAt is automatically set in constructor
        $createdAt = $this->contactMessage->getCreatedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $createdAt);
        $this->assertNotNull($createdAt);
    }

    public function testDefaultStatus(): void
    {
        // Assuming default status is 'new'
        $this->contactMessage = new ContactMessage();
        $this->assertNotNull($this->contactMessage->getStatus());
    }

    public function testMultipleProperties(): void
    {
        $this->contactMessage
            ->setName('Marie Durand')
            ->setEmail('marie@example.com')
            ->setPhone('+1-514-123-4567')
            ->setSubject('travail')
            ->setMessage('I am interested in work opportunities')
            ->setStatus('read');

        $this->assertEquals('Marie Durand', $this->contactMessage->getName());
        $this->assertEquals('marie@example.com', $this->contactMessage->getEmail());
        $this->assertEquals('+1-514-123-4567', $this->contactMessage->getPhone());
        $this->assertEquals('travail', $this->contactMessage->getSubject());
        $this->assertEquals('I am interested in work opportunities', $this->contactMessage->getMessage());
        $this->assertEquals('read', $this->contactMessage->getStatus());
    }
}
