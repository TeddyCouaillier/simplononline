<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilesRepository")
 */
class Files
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserFiles", mappedBy="files", cascade={"persist","remove"})
     */
    private $userFiles;

    public function __construct()
    {
        $this->userFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $userFile->setFiles($this);
        }

        return $this;
    }

    public function removeUserFile(UserFiles $userFile): self
    {
        if ($this->userFiles->contains($userFile)) {
            $this->userFiles->removeElement($userFile);
            // set the owning side to null (unless already changed)
            if ($userFile->getFiles() === $this) {
                $userFile->setFiles(null);
            }
        }

        return $this;
    }


}
