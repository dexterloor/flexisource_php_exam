<?php

namespace App\DTO\ResponseMapper;

// DTO
use App\DTO\Response\CustomerResponse;

// Entity
use App\Entity\Customer;

class CustomerToCustomerResponse
{
    public function map(Customer $customer, $returnType = "SINGLE")
    {
        $response = new CustomerResponse($customer, $returnType);
        return $response->getResponse();
    }
}