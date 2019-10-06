<?php

namespace App\Entity;

use App\Entity\Task;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min=4, minMessage="Le titre doit faire au moins {{ limit }} caractères")
     * @Assert\Length(max=60, maxMessage="Le titre doit faire moins de {{ limit }} caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez renseigner un URL valide pour GitHub")
     */
    private $github;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(max=500, maxMessage="La description doit faire moins de {{ limit }} caractères")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="projects")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez renseigner un URL valide pour le site")
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="project", cascade={"remove"})
     */
    private $tasks;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="projects")
     */
    private $languages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projectmod")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moderator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Correction", cascade={"persist"}, mappedBy="project", orphanRemoval=true)
     */
    private $corrections;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    public function __construct()
    {
        $this->projects    = new ArrayCollection();
        $this->users       = new ArrayCollection();
        $this->tasks       = new ArrayCollection();
        $this->languages   = new ArrayCollection();
        $this->corrections = new ArrayCollection();
        $this->createdAt   = new \DateTime();
        $this->completed   = false;
    }

    public function __toString()
    {
        return $this->title;
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

    /**
     * Get the progress status (color)
     * @return string
     */
    public function getProgressColor($val)
    {
        switch(true){
            case ($val == 0):
                return 'white';
            case ($val <= 25):
                return 'red';
            case ($val <= 50):
                return 'orange';
            case( $val <= 75):
                return 'yellow';
            case ($val < 100):
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

    public function hasUser(User $user)
    {
        return $this->users->contains($user);
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

    public function clearProject()
    {
        if(!empty($this->users)){
            foreach($this->users as $user){
                $this->removeUser($user);
            }
        }
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

    /**
     * Remove all languages in the specific project
     * @return void
     */
    public function clearLanguages()
    {
        if(!empty($this->languages)){
            foreach($this->languages as $language){
                $this->removeLanguage($language);
            }
        }
    }

    /**
     * Check if the project has a specific language
     * @param Language $language
     * @return boolean
     */
    public function hasLanguage(Language $language)
    {
        return $this->languages->contains($language);
    }

    public function getModerator(): ?User
    {
        return $this->moderator;
    }

    public function setModerator(?User $moderator): self
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * @return Collection|Correction[]
     */
    public function getCorrections(): Collection
    {
        return $this->corrections;
    }

    public function addCorrection(Correction $correction): self
    {
        if (!$this->corrections->contains($correction)) {
            $this->corrections[] = $correction;
            $correction->setProject($this);
        }

        return $this;
    }

    public function removeCorrection(Correction $correction): self
    {
        if ($this->corrections->contains($correction)) {
            $this->corrections->removeElement($correction);
            // set the owning side to null (unless already changed)
            if ($correction->getProject() === $this) {
                $correction->setProject(null);
            }
        }

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Security
     * Check the specific user with the project's user
     * @param User $user
     * @return boolean
     */
    public function checkUserProject(User $user){
        foreach($this->users as $uproject){
            if($uproject == $user){
                return true;
            }
        }
        return false;
    }

}
