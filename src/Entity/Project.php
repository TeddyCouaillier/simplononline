<?php

namespace App\Entity;

use App\Entity\Task;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $github;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="projects")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project")
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="projects")
     */
    private $languages;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->projects  = new ArrayCollection();
        $this->users     = new ArrayCollection();
        $this->tasks     = new ArrayCollection();
        $this->languages = new ArrayCollection();
    }

    /**
     * Get the difference between the project creating time and now
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
                return 'Il y a '. $day .' jours';
            case ($day >= 7 && $day <= 14):
                return 'Il y a une semaine';
            case ($day >= 15 && $day <= 21):
                return 'Il y a deux semaines';
            case ($day >= 22 && $day <= 29):
                return 'Il y a trois semaines';
            case ($day >= 30 && $day <= 365):
                $month = intval($day / 30);
                return 'Il y a '. $month .' mois';
            default:
                return 'Il y a trop longtemps';
        }
    }

    /**
     * Get all the tasks by the type
     * @param integer $type task type
     * @return Task[]
     */
    public function getTaskByType(int $type)
    {
        $tasks = [];
        $i = 0;
        foreach($this->tasks as $task){
            if($task->getType() === $type){
                $tasks[$i++] = $task;
            }
        }
        return $tasks;
    }

    /**
     * Get the size of the completed tasks
     * @return integer
     */
    public function getCompletedTask()
    {
        $res = 0;
        foreach($this->tasks as $task){
            if($task->getType() === Task::COMPLETED){
                $res++;
            }
        }
        return $res;
    }

    /**
     * Get the size of the processing tasks
     * @return integer
     */
    public function getProcessingTask()
    {
        $res = 0;
        foreach($this->tasks as $task){
            if($task->getType() === Task::PROCESSING){
                $res++;
            }
        }
        return $res;
    }

    /**
     * Get the size of the todolist tasks
     * @return integer
     */
    public function getToDoTask()
    {
        $res = 0;
        foreach($this->tasks as $task){
            if($task->getType() === Task::TODOLIST){
                $res++;
            }
        }
        return $res;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

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
            $user->addProject($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeProject($this);
        }

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        if ($this->languages->contains($language)) {
            $this->languages->removeElement($language);
        }

        return $this;
    }
}
