<?php

namespace App\Tests\Repository;

use App\Entity\ServiceRequest;

class ServiceRequestRepositoryTest extends DatabaseTestCase
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        // Get the real repository from Doctrine's registry using the kernel
        $doctrinRegistry = self::getContainer()->get('doctrine');
        $this->repository = $doctrinRegistry->getRepository(ServiceRequest::class);
    }

    public function testRepositoryCanSaveServiceRequest(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('immigration')
            ->setName('Test User')
            ->setEmail('test@example.com')
            ->setPhone('+1-581-222-5712')
            ->setCountry('France')
            ->setDetails('Seeking immigration assistance')
            ->setStatus('pending');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $this->assertNotNull($request->getId());
    }

    public function testRepositoryCanFindServiceRequest(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('travail')
            ->setName('Pierre Martin')
            ->setEmail('pierre@example.com')
            ->setPhone('+1-514-123-4567')
            ->setCountry('Belgium')
            ->setDetails('Looking for work opportunities in tech')
            ->setStatus('pending');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $id = $request->getId();

        $this->entityManager->clear();

        $foundRequest = $this->repository->find($id);

        $this->assertNotNull($foundRequest);
        $this->assertEquals('Pierre Martin', $foundRequest->getName());
        $this->assertEquals('travail', $foundRequest->getType());
    }

    public function testRepositoryCanUpdateServiceRequest(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('etude')
            ->setName('Sophie Bernard')
            ->setEmail('sophie@example.com')
            ->setPhone('+1-438-555-0123')
            ->setCountry('Germany')
            ->setDetails('Interested in university programs')
            ->setStatus('pending');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $id = $request->getId();

        $request->setStatus('approved');
        $this->entityManager->flush();

        $this->entityManager->clear();

        $updatedRequest = $this->repository->find($id);
        $this->assertEquals('approved', $updatedRequest->getStatus());
    }

    public function testRepositoryCanDeleteServiceRequest(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('sponsor')
            ->setName('To Delete')
            ->setEmail('delete@example.com')
            ->setPhone('+1-581-222-5712')
            ->setCountry('Canada')
            ->setDetails('This will be deleted')
            ->setStatus('pending');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $id = $request->getId();

        $this->entityManager->remove($request);
        $this->entityManager->flush();

        $this->entityManager->clear();

        $deletedRequest = $this->repository->find($id);
        $this->assertNull($deletedRequest);
    }

    public function testRepositoryCanQueryByType(): void
    {
        for ($i = 0; $i < 2; $i++) {
            $request = new ServiceRequest();
            $request
                ->setType('immigration')
                ->setName("User $i")
                ->setEmail("user$i@example.com")
                ->setPhone('+1-581-222-5712')
                ->setCountry('France')
                ->setDetails("Details $i")
                ->setStatus('pending');

            $this->entityManager->persist($request);
        }

        $this->entityManager->flush();

        $requests = $this->repository->findBy(['type' => 'immigration']);

        $this->assertGreaterThanOrEqual(2, count($requests));
    }

    public function testRepositoryCanQueryByStatus(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('travail')
            ->setName('Status Test')
            ->setEmail('status@example.com')
            ->setPhone('+1-581-222-5712')
            ->setCountry('Italy')
            ->setDetails('Testing status query')
            ->setStatus('rejected');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $rejectedRequests = $this->repository->findBy(['status' => 'rejected']);

        $this->assertGreaterThan(0, count($rejectedRequests));
    }

    public function testRepositoryCanQueryByMultipleCriteria(): void
    {
        $request = new ServiceRequest();
        $request
            ->setType('etude')
            ->setName('Multiple Criteria')
            ->setEmail('multi@example.com')
            ->setPhone('+1-581-222-5712')
            ->setCountry('Spain')
            ->setDetails('Multiple criteria test')
            ->setStatus('approved');

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        $requests = $this->repository->findBy(['type' => 'etude', 'status' => 'approved']);

        $this->assertGreaterThan(0, count($requests));
    }
}
