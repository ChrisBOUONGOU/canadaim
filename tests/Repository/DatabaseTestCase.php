<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

abstract class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Create database schema for tests
        $this->createDatabaseSchema();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Close the EntityManager to prevent memory leaks
        $this->entityManager->close();
    }

    private function createDatabaseSchema(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        
        try {
            $schemaTool->dropSchema($metadata);
        } catch (\Exception) {
            // Table might not exist yet, ignore
        }
        
        $schemaTool->createSchema($metadata);
    }
}
