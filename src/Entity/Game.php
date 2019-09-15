<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $link;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $github;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publisher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language", inversedBy="games")
     */
    private $language;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="game", orphanRemoval=true)
     */
    private $vote;

    public function __construct(User $user)
    {
        $this->publishedAt = new \DateTime();
        $this->publisher   = $user;
        $this->vote = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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
     * @return Collection|Vote[]
     */
    public function getVote(): Collection
    {
        return $this->vote;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->vote->contains($vote)) {
            $this->vote[] = $vote;
            $vote->setGame($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->vote->contains($vote)) {
            $this->vote->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getGame() === $this) {
                $vote->setGame(null);
            }
        }

        return $this;
    }

    /**
     * Get a specific vote's like number
     * @return boolean
     */
    public function getVoteLikes()
    {
        $countLike = 0;
        foreach($this->vote as $vote){
            if($vote->getLikeType()){
                $countLike++;
            }
        }
        return $countLike;
    }

    /**
     * Get a specific vote's dislike number
     * @return boolean
     */
    public function getVoteDislikes()
    {
        $countDislike = 0;
        foreach($this->vote as $vote){
            if(!$vote->getLikeType()){
                $countDislike++;
            }
        }
        return $countDislike;
    }
}
