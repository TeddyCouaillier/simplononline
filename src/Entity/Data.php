<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataRepository")
 * @UniqueEntity("label", message="Cette donnée éxiste déjà")
 */
class Data
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Veuillez renseigner un label")
     * @Assert\Length(max=30, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserData", mappedBy="data", orphanRemoval=true)
     */
    private $userData;

    public function __construct()
    {
        $this->userData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|UserData[]
     */
    public function getUserData(): Collection
    {
        return $this->userData;
    }

    public function addUserData(UserData $userData): self
    {
        if (!$this->userData->contains($userData)) {
            $this->userData[] = $userData;
            $userData->setData($this);
        }

        return $this;
    }

    public function removeUserData(UserData $userData): self
    {
        if ($this->userData->contains($userData)) {
            $this->userData->removeElement($userData);
            // set the owning side to null (unless already changed)
            if ($userData->getData() === $this) {
                $userData->setData(null);
            }
        }

        return $this;
    }
}
