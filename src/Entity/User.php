<?php

namespace App\Entity;

use App\Entity\Skills;
use App\Entity\UserData;
use App\Entity\UserNotif;
use App\Entity\UserSkills;
use App\Entity\Notification;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email", message="Cette adresse mail est déjà utilisée")
 */
class User implements AdvancedUserInterface
{
    const USER      = 'ROLE_USER';
    const ADMIN     = 'ROLE_ADMIN';
    const FORMER    = 'ROLE_FORMER';
    const MEDIATEUR = 'ROLE_MEDIATEUR';
    const ROLES     = [
        'Administrateur' => self::ADMIN,
        'Formateur'      => self::FORMER,
        'Médiateur'      => self::MEDIATEUR
    ];

    const SUN       = 0;
    const RAIN      = 1;
    const CLOUD     = 2;
    const THUNDER   = 3;
    const SUNCLOUD  = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez renseigner un prénom")
     * @Assert\Regex(pattern="/^[a-zA-Z]+$/", message="Lettres seulement")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez renseigner un nom")
     * @Assert\Regex(pattern="/^[a-zA-Z]+$/", message="Lettres seulement")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner une adresse mail")
     * @Assert\Email(message="Veuillez renseigner une adresse mail valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=4, minMessage="Votre mot de passe doit faire au moins {{ limit }} caractères")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Chiffre seulement")
     * @Assert\Length(max=10, maxMessage="Format invalide ({{ limit }} chiffres maximum)")
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Chiffre seulement")
     * @Assert\Length(max=5, maxMessage="Format invalide ({{ limit }} chiffres maximum)")
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="Format invalide (100 caractères maximum)")
     * @Assert\Regex(pattern="/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/", message="Lettres seulement")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez renseigner un URL valide")
     */
    private $website;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(message="Veuillez renseigner un URL valide")
     */
    private $github;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Promotion", inversedBy="users")
     */
    private $promotion;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSkills", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    private $userSkills;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserFiles", mappedBy="receiver", cascade={"persist"}, orphanRemoval=true)
     */
    private $userFiles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserFiles", mappedBy="sender")
     */
    private $senderFiles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserData", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    private $userData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Help", mappedBy="publisher", cascade={"persist"}, orphanRemoval=true)
     */
    private $helps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingCourse", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    private $trainingCourse;

    /**
     * @ORM\Column(type="date")
     */
    private $lastConnect;

    /**
     * @ORM\Column(type="integer")
     */
    private $weather;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", inversedBy="users")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Task", inversedBy="users")
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="moderator")
     */
    private $projectmod;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Promotion", inversedBy="moderators")
     */
    private $promotionmod;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", cascade={"persist"}, mappedBy="sender", orphanRemoval=true)
     */
    private $notifSent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserNotif", cascade={"persist"}, mappedBy="receiver")
     */
    private $notifReceived;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"firstname","lastname"})
     */
    private $slug;


    public function __construct()
    {
        $this->userRoles      = new ArrayCollection();
        $this->userSkills     = new ArrayCollection();
        $this->userFiles      = new ArrayCollection();
        $this->senderFiles    = new ArrayCollection();
        $this->userData       = new ArrayCollection();
        $this->helps          = new ArrayCollection();
        $this->trainingCourse = new ArrayCollection();
        $this->projects       = new ArrayCollection();
        $this->tasks          = new ArrayCollection();
        $this->projectmod     = new ArrayCollection();
        $this->notifSent      = new ArrayCollection();
        $this->notifReceived  = new ArrayCollection();
        $this->lastConnect    = new \DateTime;
        $this->weather        = self::SUN;
        $this->isActive       = true;
        $this->promotionmod = new ArrayCollection();
    }

    /**
     * Add all skills to the user
     * @param Skills[] $skills
     */
    public function initializeSkills($skills)
    {
        foreach($skills as $skill){
            $userSkill = new UserSkills();
            $userSkill->setUser($this)
                      ->setSkill($skill);

            $this->addUserSkill($userSkill);
        }
    }

    /**
     * Add all datas to the user
     * @param Data[] $datas
     */
    public function initializeDatas($datas)
    {
        foreach($datas as $data){
            $userData = new UserData();
            $userData->setUser($this)
                     ->setData($data);

            $this->addUserData($userData);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(string $password): self
    {
        $this->passwordConfirm = $password;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

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

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): self
    {
        $this->github = $github;

        return $this;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getSalt() {}
    public function eraseCredentials() {}

    /**
     */
    public function getRoles()
    {
        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray();
        $roles[] = 'ROLE_USER';
        return $roles;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getFullname(): ?string
    {
        return strtoupper($this->lastname).' '.ucfirst($this->firstname);
    }

    public function getInitial()
    {
        return strtoupper($this->firstname[0].$this->lastname[0]);
    }

    public function getAvatarName(): ?string
    {
        return $this->lastname.''.$this->firstname.''.$this->id;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function hasRole()
    {
        return sizeof($this->userRoles) > 0 ? true : false;
    }

    public function checkRole($roleChecked)
    {
        foreach($this->getRoles() as $role){
            if($role === $roleChecked){
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole)
    {
        $this->userRoles[] = $userRole;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
        }

        return $this;
    }

    /**
     * @return Collection|UserSkills[]
     */
    public function getUserSkills(): Collection
    {
        return $this->userSkills;
    }

    public function addUserSkill(UserSkills $userSkill): self
    {
        if(!$this->userSkills->contains($userSkill)) {
            $this->userSkills[] = $userSkill;
            $userSkill->setUser($this);
        }

        return $this;
    }

    public function removeUserSkill(UserSkills $userSkill): self
    {
        if ($this->userSkills->contains($userSkill)) {
            $this->userSkills->removeElement($userSkill);
            // set the owning side to null (unless already changed)
            if ($userSkill->getUser() === $this) {
                $userSkill->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserFiles[]
     */
    public function getUserFiles(): Collection
    {
        return $this->userFiles;
    }

    public function addUserFile(UserFiles $userFile): self
    {
        if (!$this->userFiles->contains($userFile)) {
            $this->userFiles[] = $userFile;
            $userFile->setReceiver($this);
        }

        return $this;
    }

    public function getFiles()
    {
        $files = [];
        $i = 0;
        foreach($this->userFiles as $ufiles){
            $files[$i++] = $ufiles->getFile();
        }
        return $files;
    }

    public function getImportantFiles()
    {
        $ufiles = [];
        $i = 0;
        foreach($this->userFiles as $ufile){
            if($ufile->getImportant()){
                $ufiles[$i++] = $ufile;
            }
        }
        return $ufiles;
    }

    public function removeUserFile(UserFiles $userFile): self
    {
        if ($this->userFiles->contains($userFile)) {
            $this->userFiles->removeElement($userFile);
            // set the owning side to null (unless already changed)
            if ($userFile->getReceiver() === $this) {
                $userFile->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserFiles[]
     */
    public function getSenderFiles(): Collection
    {
        return $this->senderFiles;
    }

    public function addSenderFile(UserFiles $senderFile): self
    {
        if (!$this->senderFiles->contains($senderFile)) {
            $this->senderFiles[] = $senderFile;
            $senderFile->setSender($this);
        }

        return $this;
    }

    public function removeSenderFile(UserFiles $senderFile): self
    {
        if ($this->senderFiles->contains($senderFile)) {
            $this->senderFiles->removeElement($senderFile);
            // set the owning side to null (unless already changed)
            if ($senderFile->getSender() === $this) {
                $senderFile->setSender(null);
            }
        }

        return $this;
    }


    public function createUserFile($sender, $file, $important)
    {
        $ufile = new UserFiles();
        $ufile->setImportant($important)
              ->setReceiver($this)
              ->setSender($sender)
              ->setFiles($file);
        $this->addUserFile($ufile);
    }

    public function createUserNotif($notif)
    {
        $unotif = new UserNotif();
        $unotif->setNotification($notif)
               ->setReceiver($this);
        $this->addNotifReceived($unotif);
    }

    public function createSenderNotif($type, $target = null)
    {
        $notif = new Notification();
        $notif->setSender($this)
              ->setType($type);
        if($target != null){
            $notif->setTarget($target);
        }
        $this->addNotifSent($notif);
        return $notif;
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
            $userData->setUser($this);
        }

        return $this;
    }

    public function removeUserData(UserData $userData): self
    {
        if ($this->userData->contains($userData)) {
            $this->userData->removeElement($userData);
            // set the owning side to null (unless already changed)
            if ($userData->getUser() === $this) {
                $userData->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Help[]
     */
    public function getHelps(): Collection
    {
        return $this->helps;
    }

    public function addHelp(Help $help): self
    {
        if (!$this->helps->contains($help)) {
            $this->helps[] = $help;
            $help->setPublisher($this);
        }

        return $this;
    }

    public function removeHelp(Help $help): self
    {
        if ($this->helps->contains($help)) {
            $this->helps->removeElement($help);
            // set the owning side to null (unless already changed)
            if ($help->getPublisher() === $this) {
                $help->setPublisher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrainingCourse[]
     */
    public function getTrainingCourse(): Collection
    {
        return $this->trainingCourse;
    }

    public function addTrainingCourse(TrainingCourse $trainingCourse): self
    {
        if (!$this->trainingCourse->contains($trainingCourse)) {
            $this->trainingCourse[] = $trainingCourse;
            $trainingCourse->setUser($this);
        }

        return $this;
    }

    public function removeTrainingCourse(TrainingCourse $trainingCourse): self
    {
        if ($this->trainingCourse->contains($trainingCourse)) {
            $this->trainingCourse->removeElement($trainingCourse);
            // set the owning side to null (unless already changed)
            if ($trainingCourse->getUser() === $this) {
                $trainingCourse->setUser(null);
            }
        }

        return $this;
    }


    public function getLastConnect(): ?\DateTimeInterface
    {
        return $this->lastConnect;
    }

    public function setLastConnect(\DateTimeInterface $lastConnect): self
    {
        $this->lastConnect = $lastConnect;

        return $this;
    }

    public function getWeather(): ?int
    {
        return $this->weather;
    }

    public function setWeather(int $weather): self
    {
        $this->weather = $weather;

        return $this;
    }


    public function getWeatherIcon()
    {
        switch ($this->weather) {
            case self::SUN:
                return '<img src="/img/weather/sun.svg" class="rounded-md">';
                break;
            case self::RAIN:
                return '<img src="/img/weather/rain.svg" class="rounded-md">';
                break;
            case self::CLOUD:
                return '<img src="/img/weather/clouds.svg" class="rounded-md">';
                break;
            case self::THUNDER:
                return '<img src="/img/weather/thunder.svg" class="rounded-md">';
                break;
            case self::SUNCLOUD:
                return '<img src="/img/weather/suncloud.svg" class="rounded-md">';
                break;
            default:
                return '<p class="icon-lg">?</p>';
        }
    }

    public function getWeatherIconXs()
    {
        switch ($this->weather) {
            case self::SUN:
                return '<img src="/img/weather/sun.svg" class="rounded-xs">';
                break;
            case self::RAIN:
                return '<img src="/img/weather/rain.svg" class="rounded-xs">';
                break;
            case self::CLOUD:
                return '<img src="/img/weather/clouds.svg" class="rounded-xs">';
                break;
            case self::THUNDER:
                return '<img src="/img/weather/thunder.svg" class="rounded-xs">';
                break;
            case self::SUNCLOUD:
                return '<img src="/img/weather/suncloud.svg" class="rounded-xs">';
                break;
            default:
                return '<p class="icon-lg">?</p>';
        }
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
        }

        return $this;
    }

    public function getProjectsCompleted()
    {
        $projects = [];
        foreach($this->projects as $project){
            if($project->getCompleted()){
                $projects[] = $projects;
            }
        }
        return $projects;
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
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjectmod(): Collection
    {
        return $this->projectmod;
    }

    public function addProjectmod(Project $projectmod): self
    {
        if (!$this->projectmod->contains($projectmod)) {
            $this->projectmod[] = $projectmod;
            $projectmod->setModerator($this);
        }

        return $this;
    }

    public function removeProjectmod(Project $projectmod): self
    {
        if ($this->projectmod->contains($projectmod)) {
            $this->projectmod->removeElement($projectmod);
            // set the owning side to null (unless already changed)
            if ($projectmod->getModerator() === $this) {
                $projectmod->setModerator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifSent(): Collection
    {
        return $this->notifSent;
    }

    public function addNotifSent(Notification $notifSent): self
    {
        if (!$this->notifSent->contains($notifSent)) {
            $this->notifSent[] = $notifSent;
            $notifSent->setSender($this);
        }

        return $this;
    }

    public function removeNotifSent(Notification $notifSent): self
    {
        if ($this->notifSent->contains($notifSent)) {
            $this->notifSent->removeElement($notifSent);
            // set the owning side to null (unless already changed)
            if ($notifSent->getSender() === $this) {
                $notifSent->setSender(null);
            }
        }

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
            $notifReceived->setReceiver($this);
        }

        return $this;
    }

    public function removeNotifReceived(UserNotif $notifReceived): self
    {
        if ($this->notifReceived->contains($notifReceived)) {
            $this->notifReceived->removeElement($notifReceived);
            // set the owning side to null (unless already changed)
            if ($notifReceived->getReceiver() === $this) {
                $notifReceived->setReceiver(null);
            }
        }

        return $this;
    }

    public function hasNotif()
    {
        foreach($this->notifReceived as $notif){
            if(!$notif->getSeen()){
                return true;
            }
        }
        return false;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Promotion[]
     */
    public function getPromotionmod(): Collection
    {
        return $this->promotionmod;
    }

    public function addPromotionmod(Promotion $promotionmod): self
    {
        if (!$this->promotionmod->contains($promotionmod)) {
            $this->promotionmod[] = $promotionmod;
        }

        return $this;
    }

    public function removePromotionmod(Promotion $promotionmod): self
    {
        if ($this->promotionmod->contains($promotionmod)) {
            $this->promotionmod->removeElement($promotionmod);
        }

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

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
