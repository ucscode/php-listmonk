<?php

namespace Junisan\ListmonkApi\Models;

use Junisan\ListmonkApi\Models\Utils\DatesModelTrait;
use Junisan\ListmonkApi\Models\Utils\IdModelTrait;

class ListModel
{
    use IdModelTrait;
    use DatesModelTrait;

    private ?string $name;
    private ?string $description;
    private ?bool $isPublic; // true -> public; false -> private
    private ?bool $optinSingle; //true -> single ; false -> double
    private ?bool $isActive = true; // true -> active; false -> archived

    /** @var string[] */
    private array $tags = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(?bool $isPublic): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getOptinSingle(): ?bool
    {
        return $this->optinSingle;
    }

    public function setOptinSingle(?bool $optinSingle): self
    {
        $this->optinSingle = $optinSingle;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
