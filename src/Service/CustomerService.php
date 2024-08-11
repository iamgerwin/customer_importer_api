<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\Customer;
use App\Helpers\Utility;

class CustomerService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function insert($user): Customer
    {
        if (!is_null($user)) {
            $customer = new Customer();
            $customer->setFirstName(!is_null($user['name']['first']) ? $user['name']['first'] : "");
            $customer->setLastName(!is_null($user['name']['last']) ? $user['name']['last'] : "");
            $customer->setUsername(!is_null($user['login']['username']) ? $user['login']['username'] : "");
            $customer->setPassword(!is_null($user['login']['password']) ? Utility::hash($user['login']['password']): "");
            $customer->setEmail(!is_null($user['email']) ? $user['email'] : "");
            $customer->setPhone(!is_null($user['phone']) ? $user['phone'] : "");
            $customer->setGender(!is_null($user['gender']) ? $user['gender'] : "");
            $customer->setCountry(!is_null($user['location']['country']) ? $user['location']['country'] : "");
            $customer->setCity(!is_null($user['location']['city']) ? $user['location']['city'] : "");

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;
        } else {
            throw new BadRequestHttpException('User is null.', null, Response::HTTP_BAD_REQUEST);
        }
    }

    public function getOne(int $id): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->find($id);
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Customer::class)->findAll();
    }
}