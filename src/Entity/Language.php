<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LanguageRepository")
 */
class Language
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\Length(max=25, maxMessage="Format invalide ({{ limit }} caractères maximum)")
     */
    private $label;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Help", mappedBy="language")
     */
    private $helps;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="languages")
     */
    private $projects;

    /**
     * @ORM\Column(type="string", length=50)
     * @Gedmo\Slug(fields={"label"})
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="language")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Codeblock", mappedBy="language")
     */
    private $codeblocks;

    public function __construct()
    {
        $this->helps    = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->codeblocks = new ArrayCollection();
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
            $help->setLanguage($this);
        }

        return $this;
    }

    public function removeHelp(Help $help): self
    {
        if ($this->helps->contains($help)) {
            $this->helps->removeElement($help);
            // set the owning side to null (unless already changed)
            if ($help->getLanguage() === $this) {
                $help->setLanguage(null);
            }
        }

        return $this;
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
            $project->addLanguage($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeLanguage($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->label;
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
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setLanguage($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getLanguage() === $this) {
                $game->setLanguage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Codeblock[]
     */
    public function getCodeblocks(): Collection
    {
        return $this->codeblocks;
    }

    public function addCodeblock(Codeblock $codeblock): self
    {
        if (!$this->codeblocks->contains($codeblock)) {
            $this->codeblocks[] = $codeblock;
            $codeblock->setLanguage($this);
        }

        return $this;
    }

    public function removeCodeblock(Codeblock $codeblock): self
    {
        if ($this->codeblocks->contains($codeblock)) {
            $this->codeblocks->removeElement($codeblock);
            // set the owning side to null (unless already changed)
            if ($codeblock->getLanguage() === $this) {
                $codeblock->setLanguage(null);
            }
        }

        return $this;
    }
}
