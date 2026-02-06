<?php

namespace App\Tests\Entity;

use App\Entity\ServiceRequest;
use PHPUnit\Framework\TestCase;

class ServiceRequestTest extends TestCase
{
    private ServiceRequest $serviceRequest;

    protected function setUp(): void
    {
        $this->serviceRequest = new ServiceRequest();
    }

    public function testEntityCreation(): void
    {
        $this->assertInstanceOf(ServiceRequest::class, $this->serviceRequest);
    }

    public function testSetAndGetType(): void
    {
        $type = 'immigration';
        $this->serviceRequest->setType($type);
        $this->assertEquals($type, $this->serviceRequest->getType());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Pierre Martin';
        $this->serviceRequest->setName($name);
        $this->assertEquals($name, $this->serviceRequest->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'pierre@example.com';
        $this->serviceRequest->setEmail($email);
        $this->assertEquals($email, $this->serviceRequest->getEmail());
    }

    public function testSetAndGetPhone(): void
    {
        $phone = '+1-581-222-5712';
        $this->serviceRequest->setPhone($phone);
        $this->assertEquals($phone, $this->serviceRequest->getPhone());
    }

    public function testSetAndGetCountry(): void
    {
        $country = 'France';
        $this->serviceRequest->setCountry($country);
        $this->assertEquals($country, $this->serviceRequest->getCountry());
    }

    public function testSetAndGetDetails(): void
    {
        $details = 'I am seeking employment in the tech industry';
        $this->serviceRequest->setDetails($details);
        $this->assertEquals($details, $this->serviceRequest->getDetails());
    }

    public function testSetAndGetStatus(): void
    {
        $status = 'pending';
        $this->serviceRequest->setStatus($status);
        $this->assertEquals($status, $this->serviceRequest->getStatus());
    }

    public function testGetCreatedAt(): void
    {
        // createdAt is automatically set in constructor
        $createdAt = $this->serviceRequest->getCreatedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $createdAt);
        $this->assertNotNull($createdAt);
    }

    public function testMultipleProperties(): void
    {
        $this->serviceRequest
            ->setType('etude')
            ->setName('Sophie Bernard')
            ->setEmail('sophie@example.com')
            ->setPhone('+1-438-555-0123')
            ->setCountry('Belgium')
            ->setDetails('Looking to study Engineering at Canadian university')
            ->setStatus('approved');

        $this->assertEquals('etude', $this->serviceRequest->getType());
        $this->assertEquals('Sophie Bernard', $this->serviceRequest->getName());
        $this->assertEquals('sophie@example.com', $this->serviceRequest->getEmail());
        $this->assertEquals('+1-438-555-0123', $this->serviceRequest->getPhone());
        $this->assertEquals('Belgium', $this->serviceRequest->getCountry());
        $this->assertEquals('Looking to study Engineering at Canadian university', $this->serviceRequest->getDetails());
        $this->assertEquals('approved', $this->serviceRequest->getStatus());
    }

    public function testServiceTypes(): void
    {
        $types = ['immigration', 'travail', 'etude', 'sponsor'];

        foreach ($types as $type) {
            $request = new ServiceRequest();
            $request->setType($type);
            $this->assertEquals($type, $request->getType());
        }
    }

    public function testStatusStates(): void
    {
        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($statuses as $status) {
            $request = new ServiceRequest();
            $request->setStatus($status);
            $this->assertEquals($status, $request->getStatus());
        }
    }
}
