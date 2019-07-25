<?php

namespace App\Entity;

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
     * @ORM\OneToOne(targetEntity="App\Entity\UserFiles", mappedBy="file", cascade={"persist", "remove"})
     */
    private $userFiles;

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

    public function getUserFiles(): ?UserFiles
    {
        return $this->userFiles;
    }

    public function setUserFiles(UserFiles $userFiles): self
    {
        $this->userFiles = $userFiles;

        // set the owning side of the relation if necessary
        if ($this !== $userFiles->getFile()) {
            $userFiles->setFile($this);
        }

        return $this;
    }
}
