<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeadlineRepository")
 */
class Deadline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $endAt;

    /**
     * @ORM\Column(type="text")
     */
    private $task;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserDeadline", mappedBy="deadline", orphanRemoval=true)
     */
    private $userDeadline;

    public function __construct()
    {
        $this->userDeadline = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->task;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): self
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Collection|UserDeadline[]
     */
    public function getUserDeadline(): Collection
    {
        return $this->userDeadline;
    }

    public function addUserDeadline(UserDeadline $userDeadline): self
    {
        if (!$this->userDeadline->contains($userDeadline)) {
            $this->userDeadline[] = $userDeadline;
            $userDeadline->setDeadline($this);
        }

        return $this;
    }

    public function removeUserDeadline(UserDeadline $userDeadline): self
    {
        if ($this->userDeadline->contains($userDeadline)) {
            $this->userDeadline->removeElement($userDeadline);
            // set the owning side to null (unless already changed)
            if ($userDeadline->getDeadline() === $this) {
                $userDeadline->setDeadline(null);
            }
        }

        return $this;
    }
}