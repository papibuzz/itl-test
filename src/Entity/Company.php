<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $siren;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_maj;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSiren(): int
    {
        return $this->siren;
    }

    /**
     * @param int $siren
     * @return $this
     */
    public function setSiren(int $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateMAJ(): DateTimeInterface
    {
        return $this->date_maj;
    }

    /**
     * @param DateTimeInterface $dateMAJ
     * @return $this
     */
    public function setDateMAJ(DateTimeInterface $dateMAJ): self
    {
        $this->date_maj = $dateMAJ;

        return $this;
    }
}
