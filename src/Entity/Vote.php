<?php

namespace App\Entity;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $likeType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="vote")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="vote")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    public function __construct(User $user, Game $game, bool $like)
    {
        $this->user     = $user;
        $this->game     = $game;
        $this->likeType = $like;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikeType(): ?bool
    {
        return $this->likeType;
    }

    public function setLikeType(bool $likeType): self
    {
        $this->likeType = $likeType;

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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
