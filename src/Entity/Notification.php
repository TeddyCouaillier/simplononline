<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    const TASK    = 1;
    const PROJECT = 2;
    const SKILL   = 3;
    const FILE    = 4;

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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $target;

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

    public function getTitle()
    {
        switch($this->type){
            case self::TASK:
                return 'Vous avez été assigné à une tache';
                break;
            case self::PROJECT:
                return 'Vous avez rejoint un projet';
                break;
            case self::SKILL:
                return 'Vos compétences ont été modifiées';
                break;
            case self::FILE:
                return 'Vous avez reçu un fichier';
                break;
        }
    }

    /**
     * Get the difference between the project creating time and now
     * @return String
     */
    public function getInterval()
    {
        $time_diff = date_diff($this->sentAt, new \DateTime());
        $format = "Aujourd'hui";
        if ($time_diff->d == 1) {
            $format = "Hier";
        }
        if ($time_diff->d > 1) {
            $format = "Il y a %d jours";
        }
        if ($time_diff->m > 0) {
            $format = "Il y a %m mois";
        }
        if ($time_diff->y > 0) {
            $format = "Il y a %m ans";
        }
        return $time_diff->format($format);
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }
}
