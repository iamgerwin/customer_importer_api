<?php
namespace App\Tests;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Helpers\Utility;
use App\Entity\Customer;
use App\Service\CustomerService;

class CustomerServiceTest extends TestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?CustomerService $customerService;
    private ?EntityRepository $customerRepository;

    /**
     * @throws Exception
     */
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
            'name' => ['first' => 'Sachi', 'last' => 'Alas'],
            'email' => 'sachi.alas@test.com',
            'login' => ['username' => 'sachi', 'password' => '12345678'],
            'gender' => 'female',
            'location' => ['country' => 'Australia', 'city' => 'Darwin'],
            'phone' => '0612-345-678',
        ];
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Customer::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $customer = $this->customerService->insert($user);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('Sachi', $customer->getFirstName());
        $this->assertEquals('Alas', $customer->getLastName());
        $this->assertEquals('sachi', $customer->getUsername());
        $this->assertEquals(Utility::hash('12345678'), $customer->getPassword());
        $this->assertEquals('sachi.alas@test.com', $customer->getEmail());
        $this->assertEquals('female', $customer->getGender());
        $this->assertEquals('0612-345-678', $customer->getPhone());
        $this->assertEquals('Australia', $customer->getCountry());
        $this->assertEquals('Darwin', $customer->getCity());
    }

    public function testInsertNullUser(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('User is null.');
        $this->customerService->insert(null);
    }

    public function testInsertIncompleteUser(): void
    {
        $user = [
            'name' => ['first' => null, 'last' => 'Alas'],
            'login' => ['username' => null, 'password' => null],
            'email' => null,
            'phone' => null,
            'gender' => null,
            'location' => ['country' => 'Australia', 'city' => null],
        ];

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Customer::class));
        $this->entityManager->expects($this->once())
            ->method('flush');

        $customer = $this->customerService->insert($user);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('', $customer->getFirstName());
        $this->assertEquals('Alas', $customer->getLastName());
        $this->assertEquals('', $customer->getUsername());
        $this->assertEquals('', $customer->getPassword());
        $this->assertEquals('', $customer->getEmail());
        $this->assertEquals('', $customer->getPhone());
        $this->assertEquals('', $customer->getGender());
        $this->assertEquals('Australia', $customer->getCountry());
        $this->assertEquals('', $customer->getCity());
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
