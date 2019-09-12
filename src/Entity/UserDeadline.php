<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserDeadlineRepository")
 */
class UserDeadline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Deadline", inversedBy="userDeadline", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $deadline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userDeadline")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->validate = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    public function getDeadline(): ?Deadline
    {
        return $this->deadline;
    }

    public function setDeadline(?Deadline $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
