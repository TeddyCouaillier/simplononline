<?php

namespace App\Entity;

use App\Entity\Skills;
use App\Entity\UserData;
use App\Entity\UserSkills;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email", message="Cette adresse mail est déjà utilisée")
 */
class User implements UserInterface
{
    const USER      = 'ROLE_USER';
    const ADMIN     = 'ROLE_ADMIN';
    const MEDIATEUR = 'ROLE_MEDIATEUR';

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

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->userSkills = new ArrayCollection();
        $this->userFiles = new ArrayCollection();
        $this->senderFiles = new ArrayCollection();
        $this->userData = new ArrayCollection();
        $this->helps = new ArrayCollection();
    }

    public function updateAvatar()
    {
        if($this->avatar == null){
            $this->avatar = "avatar.png";
        }
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
        if (!$this->userSkills->contains($userSkill)) {
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
}
