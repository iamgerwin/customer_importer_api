<?php
namespace App\Tests;

use App\Transformer\Customer\CustomerMapperResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Customer;
use App\Service\CustomerService;

class CustomerControllerTest extends WebTestCase
{
    private CustomerService $customerService;
    private CustomerMapperResponse $customerMapperResponse;

    public function testGetCustomersSuccess(): void
    {
        try {
            $client = static::createClient();

            $customerService = $this->createMock(CustomerService::class);
            $customerMapperResponse = $this->createMock(CustomerMapperResponse::class);

            $customer1 = $this->createMock(Customer::class);
            $customer1->method('getFirstName')->willReturn('John');
            $customer1->method('getLastName')->willReturn('Alas');
            $customer1->method('getEmail')->willReturn('john.alas@test.com');
            $customer1->method('getCountry')->willReturn('Australia');

            $customer2 = $this->createMock(Customer::class);
            $customer2->method('getFirstName')->willReturn('Mariz');
            $customer2->method('getLastName')->willReturn('Alas');
            $customer2->method('getEmail')->willReturn('jane.alas@test.com');
            $customer2->method('getCountry')->willReturn('Australia');

            $customerService->method('getAll')->willReturn([$customer1, $customer2]);
            $customerMapperResponse->method('map')->willReturnOnConsecutiveCalls(
                ['fullName' => 'John Alas', 'email' => 'john.alas@test.com', 'country' => 'Australia'],
                ['fullName' => 'Mariz Alas', 'email' => 'mariz.alas@test.com', 'country' => 'Australia']
            );

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerMapperResponse::class, $customerMapperResponse);
            $client->request('GET', '/customers');
            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $responseData);
            $this->assertCount(2, $responseData['data']);
            $this->assertEquals(['fullName' => 'John Alas', 'email' => 'john.alas@test.com', 'country' => 'Australia'], $responseData['data'][0]);
            $this->assertEquals(['fullName' => 'Mariz Alas', 'email' => 'mariz.alas@test.com', 'country' => 'Australia'], $responseData['data'][1]);
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

            $customerMapperResponse = $this->createMock(CustomerMapperResponse::class);

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerMapperResponse::class, $customerMapperResponse);

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
            $customer->method('getFirstName')->willReturn('John');
            $customer->method('getLastName')->willReturn('Alas');
            $customer->method('getEmail')->willReturn('john.alas@test.com');
            $customer->method('getPhone')->willReturn('63123-456-7890');
            $customer->method('getUsername')->willReturn('johnalas');
            $customer->method('getGender')->willReturn('Male');
            $customer->method('getCountry')->willReturn('Australia');
            $customer->method('getCity')->willReturn('Orange');

            $customerService = $this->createMock(CustomerService::class);
            $customerService->method('getOne')->willReturn($customer);

            $customerMapperResponse = $this->createMock(CustomerMapperResponse::class);
            $customerMapperResponse->method('map')->willReturn([
                'fullName' => 'John Alas',
                'email' => 'john.alas@test.com',
                'username' => 'johnalas',
                'phone' => '63123-456-7890',
                'gender' => 'Male',
                'country' => 'Australia',
                'city' => 'Orange',
            ]);

            $client->getContainer()->set(CustomerService::class, $customerService);
            $client->getContainer()->set(CustomerMapperResponse::class, $customerMapperResponse);

            $client->request('GET', '/customers/1');

            $response = $client->getResponse();
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $responseData = json_decode($response->getContent(), true);

            $this->assertArrayHasKey('data', $responseData);
            $this->assertEquals('John Alas', $responseData['data']['fullName']);
            $this->assertEquals('johnalas', $responseData['data']['username']);
            $this->assertEquals('john.alas@test.com', $responseData['data']['email']);
            $this->assertEquals('63123-456-7890', $responseData['data']['phone']);
            $this->assertEquals('Male', $responseData['data']['gender']);
            $this->assertEquals('Australia', $responseData['data']['country']);
            $this->assertEquals('Orange', $responseData['data']['city']);
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

            $client->request('GET', '/customers/01010101');

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

            $client->request('GET', '/customers/x');

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
