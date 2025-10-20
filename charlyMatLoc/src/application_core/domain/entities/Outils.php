<?php

namespace charlyMatLoc\core\application_core\domain\entities;

namespace charlyMatLoc\src\application_core\domain\entities;

class Outil
{
    private int $id;
    private string $nom;
    private ?string $description;
    private float $tarif;
    private string $categorie;

    public function __construct(
        int     $id,
        string  $nom,
        ?string $description,
        float   $tarif,
        string  $categorie,
    )
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->tarif = $tarif;
        $this->categorie = $categorie;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTarif(): float
    {
        return $this->tarif;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'tarif' => $this->tarif,
            'categorie' => $this->categorie,
        ];
    }
}
