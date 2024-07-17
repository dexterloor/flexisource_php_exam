<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

// Exceptions
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

// Doctrine
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

// Entity
use App\Entity\Customer;

// Service
use App\Service\CustomerService;

// Utility
use App\Utility\Utility as U;

class CustomerServiceTest extends TestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?CustomerService $customerService;
    private $customerRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->customerRepository = $this->createMock(EntityRepository::class);

        $this->entityManager->method('getRepository')
            ->willReturn($this->customerRepository);

        $this->customerService = new CustomerService($this->entityManager);
    }

    public function testInsertSuccess(): void
    {
        $user = [
            'name' => ['first' => 'John', 'last' => 'Doe'],
            'email' => 'john.doe@example.com',
            'login' => ['username' => 'johndoe', 'password' => 'password123'],
            'gender' => 'male',
            'location' => ['country' => 'Australia', 'city' => 'Sydney'],
            'phone' => '0412-345-678',
        ];

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Customer::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $customer = $this->customerService->insert($user);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('John', $customer->getFname());
        $this->assertEquals('Doe', $customer->getLname());
        $this->assertEquals('john.doe@example.com', $customer->getEmail());
        $this->assertEquals('johndoe', $customer->getUsername());
        // Assuming U::hash($user['login']['password']) hashes the password correctly
        $this->assertEquals(U::hash('password123'), $customer->getPassword());
        $this->assertEquals('male', $customer->getGender());
        $this->assertEquals('Australia', $customer->getCountry());
        $this->assertEquals('Sydney', $customer->getCity());
        $this->assertEquals('0412-345-678', $customer->getPhone());
    }

    public function testInsertNullUser(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Cannot insert null user.');

        $this->customerService->insert(null);
    }

    public function testInsertIncompleteUser(): void
    {
        $user = [
            'name' => ['first' => null, 'last' => 'Doe'],
            'email' => null,
            'login' => ['username' => null, 'password' => null],
            'gender' => null,
            'location' => ['country' => 'Australia', 'city' => null],
            'phone' => null,
        ];

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Customer::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $customer = $this->customerService->insert($user);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('', $customer->getFname());
        $this->assertEquals('Doe', $customer->getLname());
        $this->assertEquals('', $customer->getEmail());
        $this->assertEquals('', $customer->getUsername());
        $this->assertEquals('', $customer->getPassword());
        $this->assertEquals('', $customer->getGender());
        $this->assertEquals('Australia', $customer->getCountry());
        $this->assertEquals('', $customer->getCity());
        $this->assertEquals('', $customer->getPhone());
    }

    public function testGetAll(): void
    {
        $customers = [
            $this->createMock(Customer::class),
            $this->createMock(Customer::class),
        ];

        $this->customerRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($customers);

        $result = $this->customerService->getAll();

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Customer::class, $result);
    }

    public function testGetAllEmpty(): void
    {
        $this->customerRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $result = $this->customerService->getAll();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetOne(): void
    {
        $customer = $this->createMock(Customer::class);

        $this->customerRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($customer);

        $result = $this->customerService->getOne(1);

        $this->assertInstanceOf(Customer::class, $result);
    }

    public function testGetOneNotFound(): void
    {
        $this->customerRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $result = $this->customerService->getOne(1);

        $this->assertNull($result);
    }
}
