<?php
namespace App\Controller;

use App\Service\CustomerService;
use App\Transformer\Customer\CustomerMapperResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'get_customers', methods: ['GET'])]
    public function getCustomers(
        CustomerService $customerService,
        CustomerMapperResponse $customerMapperResponse
    ): JsonResponse
    {
        $customers = $customerService->getAll();
        $return = [];

        foreach ($customers as $customer) {
            $return[] = $customerMapperResponse->map($customer, "COLLECTION");
        }

        return $this->json([
            'data' => $return
        ]);
    }

    #[Route('/customers/{id}', name: 'get_customer', methods: ['GET'])]
    public function getOneCustomer(
        CustomerService        $customerService,
        CustomerMapperResponse $customerMapperResponse,
        ?int                   $id
    ): JsonResponse
    {
        if ($id <= 0) {
            throw new BadRequestHttpException("Invalid customer ID requested.", null, 400);
        }

        $customer = $customerService->getOne($id);
        if (is_null($customer)) {
            throw new NotFoundHttpException('Customer not found.', null, 404);
        }

        $return = $customerMapperResponse->map($customer);

        return $this->json([
            'data' => $return
        ]);
    }
}

