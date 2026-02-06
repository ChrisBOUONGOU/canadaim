<?php

namespace App\Tests\Repository;

use App\Entity\ContactMessage;

class ContactMessageRepositoryTest extends DatabaseTestCase
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        // Get the real repository from Doctrine's registry using the kernel
        $doctrinRegistry = self::getContainer()->get('doctrine');
        $this->repository = $doctrinRegistry->getRepository(ContactMessage::class);
    }

    public function testRepositoryCanSaveContactMessage(): void
    {
        $contactMessage = new ContactMessage();
        $contactMessage
            ->setName('Test User')
            ->setEmail('test@example.com')
            ->setPhone('+1-581-222-5712')
            ->setSubject('immigration')
            ->setMessage('Test message for immigration inquiry')
            ->setStatus('new');

        $this->entityManager->persist($contactMessage);
        $this->entityManager->flush();

        $this->assertNotNull($contactMessage->getId());
    }

    public function testRepositoryCanFindContactMessage(): void
    {
        $contactMessage = new ContactMessage();
        $contactMessage
            ->setName('John Doe')
            ->setEmail('john@example.com')
            ->setPhone('+1-438-555-0123')
            ->setSubject('travail')
            ->setMessage('Looking for work opportunities')
            ->setStatus('new');

        $this->entityManager->persist($contactMessage);
        $this->entityManager->flush();

        $id = $contactMessage->getId();

        // Clear the identity map to ensure we're fetching from database
        $this->entityManager->clear();

        $foundMessage = $this->repository->find($id);

        $this->assertNotNull($foundMessage);
        $this->assertEquals('John Doe', $foundMessage->getName());
        $this->assertEquals('john@example.com', $foundMessage->getEmail());
    }

    public function testRepositoryCanUpdateContactMessage(): void
    {
        $contactMessage = new ContactMessage();
        $contactMessage
            ->setName('Original Name')
            ->setEmail('original@example.com')
            ->setPhone('+1-581-222-5712')
            ->setSubject('immigration')
            ->setMessage('Original message')
            ->setStatus('new');

        $this->entityManager->persist($contactMessage);
        $this->entityManager->flush();

        $id = $contactMessage->getId();

        // Update the message
        $contactMessage->setStatus('read');
        $contactMessage->setName('Updated Name');
        $this->entityManager->flush();

        $this->entityManager->clear();

        $updatedMessage = $this->repository->find($id);
        $this->assertEquals('read', $updatedMessage->getStatus());
        $this->assertEquals('Updated Name', $updatedMessage->getName());
    }

    public function testRepositoryCanDeleteContactMessage(): void
    {
        $contactMessage = new ContactMessage();
        $contactMessage
            ->setName('To Delete')
            ->setEmail('delete@example.com')
            ->setPhone('+1-581-222-5712')
            ->setSubject('immigration')
            ->setMessage('This will be deleted')
            ->setStatus('new');

        $this->entityManager->persist($contactMessage);
        $this->entityManager->flush();

        $id = $contactMessage->getId();

        // Delete the message
        $this->entityManager->remove($contactMessage);
        $this->entityManager->flush();

        $this->entityManager->clear();

        $deletedMessage = $this->repository->find($id);
        $this->assertNull($deletedMessage);
    }

    public function testRepositoryCanQueryBySubject(): void
    {
        // Create multiple messages with different subjects
        for ($i = 0; $i < 3; $i++) {
            $message = new ContactMessage();
            $message
                ->setName("User $i")
                ->setEmail("user$i@example.com")
                ->setPhone('+1-581-222-5712')
                ->setSubject('immigration')
                ->setMessage("Message $i")
                ->setStatus('new');

            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();

        // Query for immigration messages
        $messages = $this->repository->findBy(['subject' => 'immigration']);

        $this->assertGreaterThanOrEqual(3, count($messages));
    }

    public function testRepositoryCanQueryByStatus(): void
    {
        $message = new ContactMessage();
        $message
            ->setName('Status Test')
            ->setEmail('status@example.com')
            ->setPhone('+1-581-222-5712')
            ->setSubject('immigration')
            ->setMessage('Testing status query')
            ->setStatus('archived');

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $archivedMessages = $this->repository->findBy(['status' => 'archived']);

        $this->assertGreaterThan(0, count($archivedMessages));
    }
}
