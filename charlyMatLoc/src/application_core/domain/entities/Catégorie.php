<?php


namespace charlyMatLoc\src\application_core\domain\entities;

class Catégorie
{
    private int $id;
    private string $nom;
    private string $description;
    public function __construct(
        int     $id,
        string  $nom,
        string $description
    )
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description
        ];
    }
}
