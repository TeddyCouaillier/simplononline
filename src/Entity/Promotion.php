<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromotionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Promotion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min=5, minMessage="Format invalide ({{ limit }} caractères minimum)")
     * @Assert\Length(max=20, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"label"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $nickname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="promotion")
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     */
    private $current;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="promotionmod")
     */
    private $moderators;

    /**
     * @ORM\PreRemove
     */
    public function removeUsers()
    {
        foreach($this->getUsers() as $user){
            $this->removeUser($user);
        }
    }

    public function __construct()
    {
        $this->users      = new ArrayCollection();
        $this->moderators = new ArrayCollection();
        $this->current    = false;
    }

    public function __toString()
    {
        return $this->getLabel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

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
            $user->setPromotion($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getPromotion() === $this) {
                $user->setPromotion(null);
            }
        }

        return $this;
    }

    public function getCurrent(): ?bool
    {
        return $this->current;
    }

    public function setCurrent(bool $current): self
    {
        $this->current = $current;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getModerators(): Collection
    {
        return $this->moderators;
    }

    public function addModerator(User $moderator): self
    {
        if (!$this->moderators->contains($moderator)) {
            $this->moderators[] = $moderator;
            $moderator->addPromotionmod($this);
        }

        return $this;
    }

    public function removeModerator(User $moderator): self
    {
        if ($this->moderators->contains($moderator)) {
            $this->moderators->removeElement($moderator);
            $moderator->removePromotionmod($this);
        }

        return $this;
    }

    /**
     * Check if the promo has a specific moderator
     * @param User $moderator
     * @return boolean
     */
    public function hasModerator(User $moderator)
    {
        return $this->moderators->contains($moderator);
    }

    /**
     * Remove all promo's moderators
     * @return void
     */
    public function clearModerator()
    {
        if(!empty($this->moderators)){
            foreach($this->moderators as $moderator){
                $this->removeModerator($moderator);
            }
        }
    }

    /**
     * Get promotion's mediators
     * @return User[]
     */
    public function getMediators()
    {
        $mediators = [];
        foreach($this->moderators as $moderator){
            foreach($moderator->getRoles() as $role){
                if($role === User::MEDIATEUR){
                    $mediators[] = $moderator;
                }
            }
        }
        return $mediators;
    }

    /**
     * Get promotion's formers
     * @return User[]
     */
    public function getFormers()
    {
        $formers = [];
        foreach($this->moderators as $moderator){
            foreach($moderator->getRoles() as $role){
                if($role === User::FORMER){
                    $formers[] = $moderator;
                }
            }
        }
        return $formers;
    }
}
