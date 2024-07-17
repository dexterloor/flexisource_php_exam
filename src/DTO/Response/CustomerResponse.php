<?php

namespace App\DTO\Response;

// Entity
use App\Entity\Customer;

class CustomerResponse
{
     private $returnType;
     private $fname;
     private $lname;
     private $email;
     private $username;
     private $gender;
     private $country;
     private $city;
     private $phone;

    public function __construct(Customer $customer, $returnType = "SINGLE")
    {
        $this->returnType = $returnType;
        $this->fname = $customer->getFname();
        $this->lname = $customer->getLname();
        $this->email = $customer->getEmail();
        $this->country = $customer->getCountry();

        if ($returnType == "SINGLE") {
            $this->username = $customer->getUsername();
            $this->gender = $customer->getGender();
            $this->city = $customer->getCity();
            $this->phone = $customer->getPhone();
        }
    }

    /**
     * @return string|null
     */
    public function getFname(): ?string
    {
        return $this->fname;
    }

    /**
     * @return string|null
     */
    public function getLname(): ?string
    {
        return $this->lname;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getResponse()
    {
        if ($this->returnType == "SINGLE") {
            return [
                'full_name' => $this->getFname().' '.$this->getLname(),
                'email' => $this->getEmail(),
                'username' => $this->getUsername(),
                'gender' => $this->getGender(),
                'country' => $this->getCountry(),
                'city' => $this->getCity(),
                'phone' => $this->getPhone()
            ];
        } else {
            return [
                'full_name' => $this->getFname().' '.$this->getLname(),
                'email' => $this->getEmail(),
                'country' => $this->getCountry()
            ];
        }
    }
}