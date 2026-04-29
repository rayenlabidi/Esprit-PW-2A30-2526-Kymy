<?php
// Model/EventCategory.php — Entity class for Event Category

class EventCategory
{
    private ?int   $id = null;
    private string $name;
    private string $description;

    public function __construct(string $name, string $description)
    {
        $this->name        = $name;
        $this->description = $description;
    }

    // ── Getters ──
    public function getId(): ?int          { return $this->id; }
    public function getName(): string      { return $this->name; }
    public function getDescription(): string { return $this->description; }

    // ── Setters ──
    public function setId(?int $id): void          { $this->id = $id; }
    public function setName(string $v): void       { $this->name = $v; }
    public function setDescription(string $v): void { $this->description = $v; }
}
