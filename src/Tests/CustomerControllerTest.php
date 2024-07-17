<?php

namespace App\Tests;

// Core
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

// DTO Mapper
use App\DTO\ResponseMapper\CustomerToCustomerResponse;

// Entity
use App\Entity\Customer;

// Service
use App\Service\CustomerService;

class CustomerControllerTest extends WebTestCase
{
    private CustomerService $customerService;
    private CustomerToCustomerResponse $customerToCustomerResponse;

    public function testGetCustomersSuccess(): void
    {
        try {
            $client = static::createClient();

            $customerService = $this->createMock(CustomerService::class);
            $customerToCustomerResponse = $this->createMock(CustomerToCustomerResponse::class);

            $customer1 = $this->createMock(Customer::class);
            $customer1->method('getFname')->willReturn('John');
            $customer1->method('getLname')->willReturn('Doe');
            $customer1->method('getEmail')->willReturn('john.doe@example.com');
            $customer1->method('getCountry')->willReturn('Australia');

            $customer2 = $this->createMock(Customer::class);
            $customer2->method('getFname')->willReturn('Jane');
            $customer2->method('getLname')->willReturn('Smith');
            $customer2->method('getEmail')->willReturn('jane.smith@example.com');
            $customer2->method('getCountry')->willReturn('Australia');

            $customerService->method('getAll')->willReturn([$customer1, $customer2]);
            $customerToCustomerResponse->method('map')->willReturnOnConsecutiveCalls(
                ['fullName' => 'John Doe', 'email' => 'john.doe@example.com', 'country' => 'Australia'],
                ['fullName' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'country' => 'Australia']
            );

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerToCustomerResponse::class, $customerToCustomerResponse);
            $client->request('GET', '/customers');
            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $responseData);
            $this->assertCount(2, $responseData['data']);
            $this->assertEquals(['fullName' => 'John Doe', 'email' => 'john.doe@example.com', 'country' => 'Australia'], $responseData['data'][0]);
            $this->assertEquals(['fullName' => 'Jane Smith', 'email' => 'jane.smith@example.com', 'country' => 'Australia'], $responseData['data'][1]);
        } catch (\Exception $e) {
            var_dump($e);
            exit($e->getCode());
        } finally {
            restore_exception_handler();
        }
    }

    public function testGetCustomersEmpty(): void
    {
        try {
            $client = static::createClient();

            $customerService = $this->createMock(CustomerService::class);
            $customerService->method('getAll')->willReturn([]);

            $customerToCustomerResponse = $this->createMock(CustomerToCustomerResponse::class);

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerToCustomerResponse::class, $customerToCustomerResponse);

            $client->request('GET', '/customers');

            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $responseData);
            $this->assertCount(0, $responseData['data']); // Expecting an empty list
        } catch (\Exception $e) {
            var_dump($e);
            exit($e->getCode());
        } finally {
            restore_exception_handler();
        }
    }

    public function testGetOneCustomerSuccess(): void
    {
        try {
            $client = static::createClient();

            $customer = $this->createMock(Customer::class);
            $customer->method('getFname')->willReturn('John');
            $customer->method('getLname')->willReturn('Doe');
            $customer->method('getEmail')->willReturn('john.doe@example.com');
            $customer->method('getUsername')->willReturn('johndoe');
            $customer->method('getGender')->willReturn('Male');
            $customer->method('getCountry')->willReturn('Australia');
            $customer->method('getCity')->willReturn('Sydney');
            $customer->method('getPhone')->willReturn('123-456-7890');

            $customerService = $this->createMock(CustomerService::class);
            $customerService->method('getOne')->willReturn($customer);

            $customerToCustomerResponse = $this->createMock(CustomerToCustomerResponse::class);
            $customerToCustomerResponse->method('map')->willReturn([
                'fullName' => 'John Doe',
                'email' => 'john.doe@example.com',
                'username' => 'johndoe',
                'gender' => 'Male',
                'country' => 'Australia',
                'city' => 'Sydney',
                'phone' => '123-456-7890'
            ]);

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerToCustomerResponse::class, $customerToCustomerResponse);

            $client->request('GET', '/customers/1');

            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $responseData);
            $this->assertEquals('John Doe', $responseData['data']['fullName']);
            $this->assertEquals('john.doe@example.com', $responseData['data']['email']);
            $this->assertEquals('johndoe', $responseData['data']['username']);
            $this->assertEquals('Male', $responseData['data']['gender']);
            $this->assertEquals('Australia', $responseData['data']['country']);
            $this->assertEquals('Sydney', $responseData['data']['city']);
            $this->assertEquals('123-456-7890', $responseData['data']['phone']);
        } catch (\Exception $e) {
            var_dump($e);
            exit($e->getCode());
        } finally {
            restore_exception_handler();
        }
    }

    public function testGetOneCustomerNotFound(): void
    {
        try {
            $client = static::createClient();

            $customerService = $this->createMock(CustomerService::class);
            $customerService->method('getOne')->willReturn(null);

            $client->getContainer()->set(CustomerService::class, $customerService);

            $client->request('GET', '/customers/999'); // Assuming 999 does not exist

            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        } catch (\Exception $e) {
            var_dump($e);
            exit($e->getCode());
        } finally {
            restore_exception_handler();
        }
    }

    public function testGetOneCustomerInvalidId(): void
    {
        try {
            $client = static::createClient();

            $client->request('GET', '/customers/-1'); // Invalid ID

            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        } catch (\Exception $e) {
            var_dump($e);
            exit($e->getCode());
        } finally {
            restore_exception_handler();
        }
    }
}
