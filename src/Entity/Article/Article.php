<?php

namespace App\Entity\Article;

use App\Entity\User\User;
use App\Repository\Article\ArticleRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $publish = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishAt = null;

    #[ORM\Column]
    private ?int $see = null;

    #[ORM\ManyToOne(inversedBy: 'articleAuthor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'articleAuthorEdit')]
    private ?User $authorEdit = null;

    #[Vich\UploadableField(mapping: 'article_pictures', fileNameProperty: 'pictureName')]
    private ?File $pictureFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureName = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->publish = false;
        $this->see = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(bool $publish): static
    {
        $this->publish = $publish;

        return $this;
    }

    public function getPublishAt(): ?DateTimeImmutable
    {
        return $this->publishAt;
    }

    public function setPublishAt(?DateTimeImmutable $publishAt): static
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    public function getSee(): ?int
    {
        return $this->see;
    }

    public function setSee(int $see): static
    {
        $this->see = $see;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getAuthorEdit(): ?User
    {
        return $this->authorEdit;
    }

    public function setAuthorEdit(?User $authorEdit): static
    {
        $this->authorEdit = $authorEdit;

        return $this;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile): static
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): static
    {
        $this->pictureName = $pictureName;

        return $this;
    }
}
