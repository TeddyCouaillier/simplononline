<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserFilesRepository")
 */
class UserFiles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Files", inversedBy="userFiles", cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $files;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="senderFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_send;

    /**
     * @ORM\Column(type="boolean")
     */
    private $important;


    public function __construct()
    {
        $this->date_send = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getDateSend(): ?\DateTimeInterface
    {
        return $this->date_send;
    }

    public function setDateSend(\DateTimeInterface $date_send): self
    {
        $this->date_send = $date_send;

        return $this;
    }

    public function getImportant(): ?bool
    {
        return $this->important;
    }

    public function setImportant(bool $important): self
    {
        $this->important = $important;

        return $this;
    }

    public function getFiles(): ?Files
    {
        return $this->files;
    }

    public function setFiles(?Files $files): self
    {
        $this->files = $files;

        return $this;
    }
}
