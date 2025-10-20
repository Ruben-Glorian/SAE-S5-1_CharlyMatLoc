<?php


namespace charlyMatLoc\src\application_core\domain\entities;

class Images
{
    private int $id;
    private string $outil_id;
    private string $url;
    private string $description;

    public function __construct(
        int $id,
        string $outil_id,
        string $url,
        string  $description
    )
    {
        $this->id = $id;
        $this->outil_id = $outil_id;
        $this->url = $url;
        $this->description = $description;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }
    public function getOutilId(): string
    {
        return $this->outil_id;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'outil_id' => $this->outil_id,
            'url' => $this->url,
            'description' => $this->description
        ];
    }
}
