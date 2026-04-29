<?php
// Model/Event.php — Entity class for Event
// Follows the structure of the "personne" example

class Event
{
    private ?int    $id               = null;
    private string  $title;
    private string  $description;
    private string  $event_date;
    private string  $location;
    private bool    $is_online;
    private int     $max_participants;
    private string  $status;
    private int     $organizer_id;
    private int     $category_id;
    private ?string $image_url        = null;
    private ?string $organizer_name   = null;
    private ?string $category_name    = null;

    public function __construct(
        string $title,
        string $description,
        string $event_date,
        string $location,
        bool   $is_online,
        int    $max_participants,
        string $status,
        int    $organizer_id,
        int    $category_id,
        ?string $image_url = null
    ) {
        $this->title            = $title;
        $this->description      = $description;
        $this->event_date       = $event_date;
        $this->location         = $location;
        $this->is_online        = $is_online;
        $this->max_participants = $max_participants;
        $this->status           = $status;
        $this->organizer_id     = $organizer_id;
        $this->category_id      = $category_id;
        $this->image_url        = $image_url;
    }

    // ── Getters ──
    public function getId(): ?int            { return $this->id; }
    public function getTitle(): string       { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getEventDate(): string   { return $this->event_date; }
    public function getLocation(): string    { return $this->location; }
    public function getIsOnline(): bool      { return $this->is_online; }
    public function getMaxParticipants(): int { return $this->max_participants; }
    public function getStatus(): string      { return $this->status; }
    public function getOrganizerId(): int    { return $this->organizer_id; }
    public function getCategoryId(): int     { return $this->category_id; }
    public function getImageUrl(): ?string   { return $this->image_url; }
    public function getOrganizerName(): ?string { return $this->organizer_name; }
    public function getCategoryName(): ?string  { return $this->category_name; }

    // ── Setters ──
    public function setId(?int $id): void              { $this->id = $id; }
    public function setTitle(string $v): void          { $this->title = $v; }
    public function setDescription(string $v): void    { $this->description = $v; }
    public function setEventDate(string $v): void      { $this->event_date = $v; }
    public function setLocation(string $v): void       { $this->location = $v; }
    public function setIsOnline(bool $v): void         { $this->is_online = $v; }
    public function setMaxParticipants(int $v): void   { $this->max_participants = $v; }
    public function setStatus(string $v): void         { $this->status = $v; }
    public function setOrganizerId(int $v): void       { $this->organizer_id = $v; }
    public function setCategoryId(int $v): void        { $this->category_id = $v; }
    public function setImageUrl(?string $v): void      { $this->image_url = $v; }
    public function setOrganizerName(?string $v): void { $this->organizer_name = $v; }
    public function setCategoryName(?string $v): void  { $this->category_name = $v; }
}
