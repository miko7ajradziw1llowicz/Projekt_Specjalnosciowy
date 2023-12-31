<?php

namespace App\Entity;

use App\Repository\HotelReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\VarDumper\Cloner\Data;
use JsonSerializable;

#[ORM\Entity(repositoryClass: HotelReservationRepository::class)]
class HotelReservation implements JsonSerializable
{

    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $Lastname = null;


    #[ORM\Column]
    /**
     * @Assert\Length(
     *     min = 9,
     *     max = 11,
     *     minMessage = "Phone number must have at least 9 digits",
     *     maxMessage = "Phone number cannot have more than 11 digits"
     * )
     */
    private ?int $PhoneNumber = null;






    /**
    * @Assert\GreaterThanOrEqual("1")
    */
    #[ORM\Column]
    private ?int $HowManyAdultPeople = null;
    /**
    * @Assert\GreaterThanOrEqual("0")
    */
    #[ORM\Column(nullable: true)]
    private ?int $HowManyKids = null;
     /**
    * @Assert\GreaterThanOrEqual("today")
    */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateFrom = null;
     /**
    * @Assert\GreaterThan(propertyPath="DateFrom")
    */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateTo = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;
    public function jsonSerialize():mixed
    {
        return [
            'id' => $this->getId(),
            'Name' => $this->getName(),
            'Lastname' => $this->getLastname(),
            'PhoneNumber' => $this->getPhoneNumber(),
            'HowManyAdultPeople' => $this->getHowManyAdultPeople(),
            'HowManyKids' => $this->getHowManyKids(),
            'DateFrom' => $this->getDateFrom(),
            'DateTo' => $this->getDateTo(),
            'GetPrice'=>$this->getPrice(),
        ];
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->Lastname;
    }

    public function setLastname(string $Lastname): self
    {
        $this->Lastname = $Lastname;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->PhoneNumber;
    }

    public function setPhoneNumber(int $PhoneNumber): self
    {
        $this->PhoneNumber = $PhoneNumber;

        return $this;
    }

    public function getHowManyAdultPeople(): ?int
    {
        return $this->HowManyAdultPeople;
    }

    public function setHowManyAdultPeople(int $HowManyAdultPeople): self
    {
        $this->HowManyAdultPeople = $HowManyAdultPeople;

        return $this;
    }

    public function getHowManyKids(): ?int
    {
        return $this->HowManyKids;
    }

    public function setHowManyKids(?int $HowManyKids): self
    {
        $this->HowManyKids = $HowManyKids;

        return $this;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->DateFrom;
    }

    public function setDateFrom(\DateTimeInterface $DateFrom): self
    {
        $this->DateFrom = $DateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->DateTo;
    }

    public function setDateTo(\DateTimeInterface $DateTo): self
    {
        $this->DateTo = $DateTo;

        return $this;
    }
    
    public function getPrice()
    {
       
        $diff = date_diff($this->getDateFrom(), $this->getDateTo())->format("%d");
        $price=($this->getHowManyAdultPeople()*50+$this->getHowManyKids()*35)*intVal($diff);

        return $price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }
}
