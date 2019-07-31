<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingCourseRepository")
 */
class TrainingCourse
{
    const INTERESSE  = 'Intéressé(e)';
    const ATTENTE    = 'En attente de réponse';
    const ENTRETIEN  = 'Entretien';
    const POSITIVE   = 'Réponse positive';
    const NEGATIVE   = 'Réponse négative';
    const STATUS     = [
        self::INTERESSE => self::INTERESSE,
        self::ATTENTE   => self::ATTENTE,
        self::ENTRETIEN => self::ENTRETIEN,
        self::POSITIVE  => self::POSITIVE,
        self::NEGATIVE  => self::NEGATIVE
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $society;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $place;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $project;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sent_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="trainingCourse")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->sent_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSociety(): ?string
    {
        return $this->society;
    }

    public function setSociety(string $society): self
    {
        $this->society = $society;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getColorStatus($status = null)
    {
        switch ($this->status) {
            case self::INTERESSE:
                return 'yellow';
                break;
            case self::POSITIVE:
                return 'green';
                break;
            case self::NEGATIVE:
                return 'red';
                break;
            case self::ENTRETIEN:
                return 'purple';
                break;
            default:
                return 'blue';
        }
    }

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(?string $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(\DateTimeInterface $sent_at): self
    {
        $this->sent_at = $sent_at;

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
