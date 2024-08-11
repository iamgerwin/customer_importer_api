<?php

namespace App\Transformer\Customer;

use App\Entity\Customer;

class CustomerMapperResponse
{
    public function map(Customer $customer, $returnType = "SINGLE")
    {
        $response = new CustomerResponse($customer, $returnType);
        return $response->getResponse();
    }
}
