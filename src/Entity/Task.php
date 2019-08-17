<?php

namespace App\Entity;

use App\Entity\Subtask;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subtask", cascade={"persist","remove"}, mappedBy="task")
     */
    private $subtasks;

    public function __construct()
    {
        $this->users     = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->subtasks  = new ArrayCollection();
    }

    /**
     * Get the difference between the project creating time and now
     * @return String
     */
    public function getInterval()
    {
        $time_diff = date_diff($this->createdAt, new \DateTime());
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

    /**
     * Get the subtasks done
     * @return integer
     */
    public function getSubtasksDone()
    {
        $res = 0;
        foreach($this->subtasks as $subtask){
            if($subtask->getDone()){
                $res++;
            }
        }
        return $res;
    }

    public function checkType()
    {
        $completed = true;
        if(sizeof($this->subtasks) == 0){
            $completed = false;
        }
        foreach($this->subtasks as $subtask){
            if(!$subtask->getDone()){
                $completed = false;
            }
        }
        if($completed){
            $this->type = self::COMPLETED;
        }
    }

    /**
     * Get the progress width (%)
     * @return integer
     */
    public function getProgress()
    {
        $done  = $this->getSubtasksDone();
        $total = sizeof($this->getSubtasks());

        return $total !== 0 ? intval($done / $total * 100) : 0;
    }

    /**
     * Get the progress status (color)
     * @return string
     */
    public function getProgressColor()
    {
        switch(true){
            case ($this->getProgress() == 0):
                return 'white';
            case ($this->getProgress() <= 25):
                return 'red';
            case ($this->getProgress() <= 50):
                return 'orange';
            case($this->getProgress() <= 75):
                return 'yellow';
            case($this->getProgress() < 100):
                return 'green';
            default:
                return 'green';
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

    /**
     * @return Collection|Subtask[]
     */
    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function addSubtask(Subtask $subtask): self
    {
        if (!$this->subtasks->contains($subtask)) {
            $this->subtasks[] = $subtask;
            $subtask->setTask($this);
        }

        return $this;
    }

    public function removeSubtask(Subtask $subtask): self
    {
        if ($this->subtasks->contains($subtask)) {
            $this->subtasks->removeElement($subtask);
            // set the owning side to null (unless already changed)
            if ($subtask->getTask() === $this) {
                $subtask->setTask(null);
            }
        }

        return $this;
    }
}
