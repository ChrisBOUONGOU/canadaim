# TESTING.md - Comprehensive Testing Guide for Canadaim

## Table of Contents
1. [Getting Started](#getting-started)
2. [Running Tests](#running-tests)
3. [Test Structure](#test-structure)
4. [Writing Tests](#writing-tests)
5. [Database Testing](#database-testing)
6. [Code Coverage](#code-coverage)
7. [CI/CD Integration](#cicd-integration)
8. [Troubleshooting](#troubleshooting)

## Getting Started

### Prerequisites
- PHP 8.0+ with PHPUnit
- Symfony 6.1+
- MySQL database (separate test database)
- Composer dependencies installed

### Setup Test Environment

1. **Create Test Database Configuration**

The test environment is configured in `phpunit.xml.dist`:
```xml
<server name="APP_ENV" value="test" force="true" />
```

2. **Create Test Database** (optional, if using DatabaseTestCase)
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

## Running Tests

### Run All Tests
```bash
php bin/phpunit
```

### Run Specific Test Suite
```bash
# Entity tests only
php bin/phpunit tests/Entity/

# Form tests only
php bin/phpunit tests/Form/

# Controller tests only
php bin/phpunit tests/Controller/

# Service tests only
php bin/phpunit tests/Service/

# Repository tests only
php bin/phpunit tests/Repository/
```

### Run Specific Test File
```bash
php bin/phpunit tests/Entity/ContactMessageTest.php
```

### Run Specific Test Method
```bash
php bin/phpunit --filter testEntityCreation
```

### Run with Verbosity
```bash
# Verbose output
php bin/phpunit -v

# Very verbose output
php bin/phpunit -vv

# Debug output
php bin/phpunit --debug
```

### Run in Watch Mode (requires optional package)
```bash
php bin/phpunit --testdox
```

## Test Structure

### Directory Layout
```
tests/
├── bootstrap.php                          # Test bootstrap file
├── Entity/                                # Entity unit tests
│   ├── ContactMessageTest.php
│   └── ServiceRequestTest.php
├── Form/                                  # Form validation tests
│   ├── ContactMessageFormTypeTest.php
│   └── ServiceRequestFormTypeTest.php
├── Controller/                            # Functional controller tests
│   ├── HomeControllerTest.php
│   ├── ImmigrationControllerTest.php
│   ├── ContactControllerTest.php
│   ├── SearchControllerTest.php
│   ├── PageControllersTest.php
│   └── AdminControllerTest.php
├── Service/                               # Service unit tests
│   └── SeoMetadataServiceTest.php
└── Repository/                            # Database integration tests
    ├── DatabaseTestCase.php
    ├── ContactMessageRepositoryTest.php
    └── ServiceRequestRepositoryTest.php
```

### Test Naming Convention
- File name: `{Subject}Test.php`
- Class name: `{Subject}Test`
- Method name: `test{DescriptionOfTest}`

Example: `testContactFormValidation()`, `testRepositoryCanSaveEntity()`

## Writing Tests

### Unit Test Template
```php
<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    private $subject;

    protected function setUp(): void
    {
        // Initialize test subject
        $this->subject = new MyClass();
    }

    protected function tearDown(): void
    {
        // Clean up after test
    }

    public function testSomething(): void
    {
        // Arrange
        $expected = 'value';

        // Act
        $result = $this->subject->method();

        // Assert
        $this->assertEquals($expected, $result);
    }
}
```

### Functional Test Template
```php
<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MyControllerTest extends WebTestCase
{
    public function testPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/page');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
```

### Integration Test Template
```php
<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MyRepositoryTest extends KernelTestCase
{
    private $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()
            ->get('doctrine')
            ->getRepository(MyEntity::class);
    }

    public function testPersistence(): void
    {
        // Test database operations
    }
}
```

## Database Testing

### Using DatabaseTestCase

The `DatabaseTestCase` base class provides:
- Database transaction isolation
- EntityManager setup
- Automatic cleanup

```php
class MyRepositoryTest extends DatabaseTestCase
{
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->entityManager
            ->getRepository(MyEntity::class);
    }

    public function testCreate(): void
    {
        $entity = new MyEntity();
        $entity->setName('Test');

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->assertNotNull($entity->getId());
    }
}
```

### Transaction Rollback

Tests automatically run within database transactions that are rolled back after each test, ensuring database isolation.

### Database Fixtures

For test data, you can use:

1. **Inline Creation** (recommended for tests)
```php
$entity = new Entity();
$this->entityManager->persist($entity);
$this->entityManager->flush();
```

2. **Alice Fixtures** (for larger datasets)
```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

## Code Coverage

### Generate Coverage Report

```bash
# Text coverage report
php bin/phpunit --coverage-text

# HTML coverage report
php bin/phpunit --coverage-html=coverage/

# Clover XML report (for CI/CD)
php bin/phpunit --coverage-clover=coverage.xml
```

### View Coverage Report
```bash
# On Unix/Linux/Mac
open coverage/index.html

# On Windows
start coverage\index.html
```

### Coverage Targets
- Minimum: 70%
- Target: 80%+
- Ideal: 90%+

### Exclude Files from Coverage

In `phpunit.xml.dist`:
```xml
<coverage>
    <exclude>
        <directory>src/DataFixtures</directory>
        <directory>src/Migrations</directory>
    </exclude>
</coverage>
```

## CI/CD Integration

### GitHub Actions Example

Create `.github/workflows/tests.yml`:
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: canadaim_test
        options: >-
          --health-cmd="mysqladmin ping -h localhost"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3

    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.1
          extensions: pdo_mysql
      
      - name: Install Dependencies
        run: composer install -q
      
      - name: Create Test Database
        run: php bin/console doctrine:database:create --env=test
      
      - name: Run Migrations
        run: php bin/console doctrine:migrations:migrate --env=test -n
      
      - name: Run Tests
        run: php bin/phpunit
      
      - name: Generate Coverage
        run: php bin/phpunit --coverage-clover=coverage.xml
      
      - name: Upload Coverage
        uses: codecov/codecov-action@v2
```

### GitLab CI Example

Create `.gitlab-ci.yml`:
```yaml
stages:
  - test

test:
  stage: test
  image: php:8.1
  services:
    - mysql:8.0
  before_script:
    - composer install
    - php bin/console doctrine:database:create --env=test
    - php bin/console doctrine:migrations:migrate --env=test -n
  script:
    - php bin/phpunit
```

## Assertion Reference

### Common Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);
$this->assertNotEquals($expected, $actual);

// Type checking
$this->assertIsArray($var);
$this->assertIsString($var);
$this->assertIsInt($var);
$this->assertInstanceOf(ClassName::class, $object);

// Null checks
$this->assertNull($var);
$this->assertNotNull($var);

// Boolean checks
$this->assertTrue($condition);
$this->assertFalse($condition);

// Array checks
$this->assertArrayHasKey('key', $array);
$this->assertCount(3, $array);
$this->assertContains('value', $array);

// String checks
$this->assertStringContainsString('substring', 'string');
$this->assertStringStartsWith('prefix', 'string');
$this->assertStringEndsWith('suffix', 'string');

// Exception testing
$this->expectException(ExceptionClass::class);
$this->methodThatThrows();

// Response assertions (WebTestCase)
$this->assertResponseIsSuccessful();
$this->assertResponseStatusCodeSame(200);
$this->assertResponseRedirects('/path');
$this->assertStringContainsString('text', $client->getResponse()->getContent());
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```
SQLSTATE[HY000] [2002] Can't connect to local MySQL server
```

**Solution**: Check database credentials in `.env.test`

#### 2. Table Not Found
```
SQLSTATE[42S02]: Table 'canadaim_test.entity' doesn't exist
```

**Solution**: Run migrations for test environment
```bash
php bin/console doctrine:migrations:migrate --env=test
```

#### 3. Test Fails with Memory Error
```
Fatal error: Allowed memory size of X bytes exhausted
```

**Solution**: Increase memory limit
```bash
php -d memory_limit=512M bin/phpunit
```

#### 4. Form Tests Fail
```
RuntimeException: The test entity "" has no Doctrine metadata associated with it
```

**Solution**: Ensure entity has proper Doctrine mapping

### Debug Mode

Enable debug mode in tests:
```php
protected function setUp(): void
{
    parent::setUp();
    self::bootKernel(['debug' => true]);
}
```

### Get Detailed Error Output
```bash
php bin/phpunit -vvv --debug
```

## Performance Tips

### Speed Up Tests

1. **Use SQLite for tests** (faster than MySQL)
```yaml
# config/packages/test/doctrine.yaml
doctrine:
    dbal:
        driver: pdo_sqlite
        url: 'sqlite:///%kernel.cache_dir%/test.db'
```

2. **Disable migrations during tests**
```php
// Use schema:create instead
php bin/console doctrine:schema:create --env=test
```

3. **Run tests in parallel** (requires phpunit plugin)
```bash
composer require --dev brianium/paratest
php vendor/bin/paratest
```

## Best Practices

✅ **One assertion per test** (when possible)
✅ **Descriptive test names** - Makes failures clear
✅ **Use setUp/tearDown** - Keep tests DRY
✅ **Test edge cases** - Not just happy path
✅ **Mock external dependencies** - Keep tests isolated
✅ **Use data providers** - For parameterized tests
✅ **Keep tests fast** - Each test < 1 second
✅ **Test business logic, not framework** - Focus on application

## Advanced Topics

### Data Providers

```php
/**
 * @dataProvider dataProvider
 */
public function testWithMultipleData($input, $expected)
{
    $this->assertEquals($expected, $input);
}

public function dataProvider()
{
    return [
        ['value1', 'expected1'],
        ['value2', 'expected2'],
    ];
}
```

### Mocking

```php
$mock = $this->createMock(SomeClass::class);
$mock->method('method')
    ->willReturn('value');
```

### Testing Private Methods

```php
$reflection = new ReflectionClass(MyClass::class);
$method = $reflection->getMethod('privateMethod');
$method->setAccessible(true);
$result = $method->invokeArgs($object, $args);
```

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Symfony Testing Guide](https://symfony.com/doc/current/testing.html)
- [Best Practices for Unit Testing](https://www.toptal.com/qa/how-to-write-testable-code-and-why-it-matters)

## Support

For test-related questions or issues:
1. Check this guide first
2. Review existing test examples
3. Consult PHPUnit documentation
4. Refer to Symfony testing documentation
