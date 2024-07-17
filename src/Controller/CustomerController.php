<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

// DTOMappers
use App\DTO\ResponseMapper\CustomerToCustomerResponse;

// Service
use App\Service\CustomerService;

class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'customers_get')]
    public function getCustomers(
        CustomerService $customerService,
        CustomerToCustomerResponse $customerToCustomerResponse
    ): JsonResponse
    {
        $customers = $customerService->getAll();
        $return = [];

        foreach ($customers as $customer) {
            $return[] = $customerToCustomerResponse->map($customer, "COLLECTION");
        }

        return $this->json([
            'data' => $return
        ]);
    }

    #[Route('/customers/{id}', name: 'customers_get_one')]
    public function getOneCustomer(
        CustomerService $customerService,
        CustomerToCustomerResponse $customerToCustomerResponse,
        ?int $id
    ): JsonResponse
    {
        if ($id <= 0) {
            throw new BadRequestHttpException("Invalid customer ID requested.", null, 400);
        }

        $customer = $customerService->getOne($id);
        if (is_null($customer)) {
            throw new NotFoundHttpException('Customer not found.', null, 404);
        }

        $return = $customerToCustomerResponse->map($customer);

        return $this->json([
            'data' => $return
        ]);
    }
}
