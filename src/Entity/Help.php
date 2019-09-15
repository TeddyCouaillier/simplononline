<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelpRepository")
 * @UniqueEntity("link", message="Ce lien a déjà été publié")
 */
class Help
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Url(message="Ce lien n'est pas valide")
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=75)
     * @Assert\NotBlank
     * @Assert\Length(max=75, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="helps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publisher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="helps", cascade={"persist"})
     */
    private $language;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
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
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPublisher(): ?User
    {
        return $this->publisher;
    }

    public function setPublisher(?User $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the difference between the project creating time and now
     * @return String
     */
    public function getInterval()
    {
        $time_diff = date_diff($this->created_at, new \DateTime());

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
}
