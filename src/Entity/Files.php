<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Veuillez choisir un fichier")
     * @Assert\File(
     *     maxSize = "5M",
     *     maxSizeMessage = "Taille autorisée : {{ limit }} {{ suffix }}",
     *     mimeTypes = {
     *         "image/*",
     *         "application/*",
     *         "text/*",
     *     },
     *     mimeTypesMessage = "Le fichier n'est pas valide"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Veuillez renseigner un titre")
     * @Assert\Length(max=60, maxMessage="Format invalide ({{ limit }} caractères maximum)")
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
