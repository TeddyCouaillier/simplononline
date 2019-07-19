<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="Cette adresse mail est déjà utilisée")
 */
class User implements UserInterface
{
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
     * @Assert\Length(min=4, minMessage="Votre mot de passe doit faire au moins 4 caractères")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Chiffre seulement")
     * @Assert\Length(max=10, maxMessage="Format invalide (10 chiffres maximum)")
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Regex(pattern="/^[0-9]+$/", message="Chiffre seulement")
     * @Assert\Length(max=5, maxMessage="Format invalide (5 chiffres maximum)")
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
     * @Assert\File(mimeTypes={ "image/*" }, mimeTypesMessage ="Format invalide")
     */
    private $avatar;

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
        return ['ROLE_USER'];
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
}
