<?php

namespace App\Transformer\Customer;

// Entity
use App\Entity\Customer;

class CustomerResponse
{
    private mixed $returnType;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $username;
    private ?string $email;
    private ?string $phone;
    private ?string $gender;
    private ?string $country;
    private ?string $city;

    public function __construct(Customer $customer, $returnType = "SINGLE")
    {
        $this->returnType = $returnType;
        $this->firstName = $customer->getFirstName();
        $this->lastName = $customer->getLastName();
        $this->email = $customer->getEmail();
        $this->country = $customer->getCountry();

        if ($returnType == "SINGLE") {
            $this->username = $customer->getUsername();
            $this->phone = $customer->getPhone();
            $this->gender = $customer->getGender();
            $this->city = $customer->getCity();
        }
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
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

    public function getResponse(): array
    {
        if ($this->returnType == "SINGLE") {
            return [
                'full_name' => $this->getFirstName().' '.$this->getLastName(),
                'email' => $this->getEmail(),
                'username' => $this->getUsername(),
                'gender' => $this->getGender(),
                'country' => $this->getCountry(),
                'city' => $this->getCity(),
                'phone' => $this->getPhone()
            ];
        } else {
            return [
                'full_name' => $this->getFirstName().' '.$this->getLastName(),
                'email' => $this->getEmail(),
                'country' => $this->getCountry()
            ];
        }
    }
}
