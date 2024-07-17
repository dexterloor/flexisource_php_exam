<?php

namespace App\Service;

// Doctrine
use Doctrine\ORM\EntityManagerInterface;

// Exception
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Entity
use App\Entity\Customer;

// Utility
use App\Utility\Utility as U;

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
            $customer->setFname(!is_null($user['name']['first']) ? $user['name']['first'] : "");
            $customer->setLname(!is_null($user['name']['last']) ? $user['name']['last'] : "");
            $customer->setEmail(!is_null($user['email']) ? $user['email'] : "");
            $customer->setUsername(!is_null($user['login']['username']) ? $user['login']['username'] : "");
            $customer->setPassword(!is_null($user['login']['password']) ? U::hash($user['login']['password']): "");
            $customer->setGender(!is_null($user['gender']) ? $user['gender'] : "");
            $customer->setCountry(!is_null($user['location']['country']) ? $user['location']['country'] : "");
            $customer->setCity(!is_null($user['location']['city']) ? $user['location']['city'] : "");
            $customer->setPhone(!is_null($user['phone']) ? $user['phone'] : "");

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;

        } else {
            throw new BadRequestHttpException('Cannot insert null user.', null, 400);
        }
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Customer::class)->findAll();
    }

    public function getOne(int $id): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->find($id);
    }
}