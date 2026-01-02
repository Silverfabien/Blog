<?php

namespace App\Entity\User;

use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $userApiId = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'author')]
    private Collection $articleAuthor;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'authorEdit')]
    private Collection $articleAuthorEdit;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author')]
    private Collection $comments;

    public function __construct()
    {
        $this->role = 'ROLE_USER';
        $this->articleAuthor = new ArrayCollection();
        $this->articleAuthorEdit = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserApiId(): ?int
    {
        return $this->userApiId;
    }

    public function setUserApiId(int $userApiId): static
    {
        $this->userApiId = $userApiId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticleAuthor(): Collection
    {
        return $this->articleAuthor;
    }

    public function addArticleAuthor(Article $articleAuthor): static
    {
        if (!$this->articleAuthor->contains($articleAuthor)) {
            $this->articleAuthor->add($articleAuthor);
            $articleAuthor->setAuthor($this);
        }

        return $this;
    }

    public function removeArticleAuthor(Article $articleAuthor): static
    {
        if ($this->articleAuthor->removeElement($articleAuthor)) {
            // set the owning side to null (unless already changed)
            if ($articleAuthor->getAuthor() === $this) {
                $articleAuthor->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticleAuthorEdit(): Collection
    {
        return $this->articleAuthorEdit;
    }

    public function addArticleAuthorEdit(Article $articleAuthorEdit): static
    {
        if (!$this->articleAuthorEdit->contains($articleAuthorEdit)) {
            $this->articleAuthorEdit->add($articleAuthorEdit);
            $articleAuthorEdit->setAuthorEdit($this);
        }

        return $this;
    }

    public function removeArticleAuthorEdit(Article $articleAuthorEdit): static
    {
        if ($this->articleAuthorEdit->removeElement($articleAuthorEdit)) {
            // set the owning side to null (unless already changed)
            if ($articleAuthorEdit->getAuthorEdit() === $this) {
                $articleAuthorEdit->setAuthorEdit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
