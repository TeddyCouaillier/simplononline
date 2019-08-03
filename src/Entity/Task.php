<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    const PROCESSING = 0;
    const TODOLIST   = 1;
    const COMPLETED  = 2;

    const TYPE = [
        "En cours" =>self::PROCESSING,
        "A faire"  =>self::TODOLIST,
        "Terminé"  =>self::COMPLETED
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="tasks")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     */
    private $project;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the difference between the task creating time and now
     * @return String
     */
    public function getInterval()
    {
        $interval = date_diff($this->createdAt, new \DateTime());
        $day = intval($interval->format('%R%a'));

        switch(true){
            case ($day == 0):
                return 'Aujourd\'hui';
            case ($day == 1):
                return 'Hier';
            case ($day > 1 && $day < 7):
                return 'Il y a'.$day.'jours';
            case ($day >= 7 && $day <= 14):
                return 'Il y a une semaine';
            case ($day >= 15 && $day <= 21):
                return 'Il y a deux semaines';
            case ($day >= 22 && $day <= 29):
                return 'Il y a trois semaines';
            case ($day >= 30 && $day <= 365):
                $month = intval($day / 30);
                return 'Il y a '.$month.' mois';
            default:
                return 'Il y a trop longtemps';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addTask($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeTask($this);
        }

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
