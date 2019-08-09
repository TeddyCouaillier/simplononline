<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sentAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notifSent")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserNotif", mappedBy="notification")
     */
    private $notifReceived;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    public function __construct()
    {
        $this->notifReceived = new ArrayCollection();
        $this->sentAt        = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Collection|UserNotif[]
     */
    public function getNotifReceived(): Collection
    {
        return $this->notifReceived;
    }

    public function addNotifReceived(UserNotif $notifReceived): self
    {
        if (!$this->notifReceived->contains($notifReceived)) {
            $this->notifReceived[] = $notifReceived;
            $notifReceived->setNotification($this);
        }

        return $this;
    }

    public function removeNotifReceived(UserNotif $notifReceived): self
    {
        if ($this->notifReceived->contains($notifReceived)) {
            $this->notifReceived->removeElement($notifReceived);
            // set the owning side to null (unless already changed)
            if ($notifReceived->getNotification() === $this) {
                $notifReceived->setNotification(null);
            }
        }

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
