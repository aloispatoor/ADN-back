<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AvatarRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: AvatarRepository::class)]
class Avatar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $image;

    /**
     * @Vich\UploadableField(mapping="avatars", fileNameProperty="avatar")
     * @var File|null
     */
    private $avatarFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    #[ORM\OneToOne(inversedBy: 'avatar', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
    * @param null|File $imageFile
    * @return User
    * @throws Exception
    */
    public function setAvatarFile(File $image = null)
    {
        $this->avatarFile = $image;
        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    public function getAvatar(): ?string
    {
        return $this->image;
    }

    public function setAvatar(?string $image)
    {
        $this->image = $image;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
}
