<?php

namespace App\Entity;

use App\Entity\Property;
use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255
    )]
    private $firstName;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255
    )]
    private $lastName;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Regex('/[0-9]{10}/', 'Le numéro de téléphone doit faire dix chiffres.')]
    private $phone;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Email()]
    private $email;

    /**
     * @var string|null
     */
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private $message;

    /**
     * @var Property|null
     */
    private $property;

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param null|string $firstName
     * @return Contact
     */
    public function setFirstName(?string $firstName): Contact
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param null|string $lastName
     * @return Contact
     */
    public function setLastName(?string $lastName): Contact
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param null|string $phone
     * @return Contact
     */
    public function setPhone(?string $phone): Contact
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return Contact
     */
    public function setEmail(?string $email): Contact
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     * @return Contact
     */
    public function setMessage(?string $message): Contact
    {
        $this->message = $message;
        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): static
    {
        $this->property = $property;

        return $this;
    }
}
