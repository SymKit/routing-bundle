<?php

declare(strict_types=1);

namespace Symkit\RoutingBundle\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symkit\RoutingBundle\Contract\RouteEntityInterface;
use Symkit\RoutingBundle\Repository\RouteRepository;
use Symkit\RoutingBundle\Validator\ValidRouteSyntax;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ValidRouteSyntax(groups: ['create', 'edit'])]
#[UniqueEntity(fields: ['name'], message: 'route.unique_name', groups: ['create', 'edit'])]
#[UniqueEntity(fields: ['path'], message: 'route.unique_path', groups: ['create', 'edit'])]
class Route implements RouteEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // @phpstan-ignore property.unusedType (Doctrine assigns after persist)

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $path = null;

    #[ORM\Column(length: 255)]
    private ?string $controller = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    /** @var array<int, string> */
    #[ORM\Column(type: 'json')]
    private array $methods = [];

    /** @var array<int, string> */
    #[ORM\Column(type: 'json')]
    private array $schemes = [];

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    private array $defaults = [];

    /** @var array<string, string> */
    #[ORM\Column(type: 'json')]
    private array $requirements = [];

    /** @var array<string, mixed> */
    #[ORM\Column(type: 'json')]
    private array $options = [];

    #[ORM\Column(nullable: true)]
    private ?float $sitemapPriority = null;

    #[ORM\Column]
    private bool $excludeFromSitemap = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $host = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $condition = null;

    public function __construct()
    {
        $this->isActive = false;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(string $controller): static
    {
        $this->controller = $controller;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @return array<int, string> */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /** @param array<int, string> $methods */
    public function setMethods(array $methods): static
    {
        $this->methods = $methods;

        return $this;
    }

    /** @return array<int, string> */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /** @param array<int, string> $schemes */
    public function setSchemes(array $schemes): static
    {
        $this->schemes = $schemes;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /** @param array<string, mixed> $defaults */
    public function setDefaults(array $defaults): static
    {
        $this->defaults = $defaults;

        return $this;
    }

    /** @return array<string, string> */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    /** @param array<string, string> $requirements */
    public function setRequirements(array $requirements): static
    {
        $this->requirements = $requirements;

        return $this;
    }

    /** @return array<string, mixed> */
    public function getOptions(): array
    {
        return $this->options;
    }

    /** @param array<string, mixed> $options */
    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getSitemapPriority(): ?float
    {
        return $this->sitemapPriority;
    }

    public function setSitemapPriority(?float $sitemapPriority): static
    {
        $this->sitemapPriority = $sitemapPriority;

        return $this;
    }

    public function isExcludeFromSitemap(): bool
    {
        return $this->excludeFromSitemap;
    }

    public function setExcludeFromSitemap(bool $excludeFromSitemap): static
    {
        $this->excludeFromSitemap = $excludeFromSitemap;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(?string $condition): static
    {
        $this->condition = $condition;

        return $this;
    }

    public function getLinkedPage(): ?object
    {
        return null;
    }

    public function getLinkedPageLabel(): ?string
    {
        return null;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
