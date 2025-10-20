<?php

namespace charlyMatLoc\core\application_core\domain\entities;

class Outil {
    private int $id;
    private string $nom;
    private ?string $description;
    private float $tarif;
    private string $categorie;
    private ?string $imageUrl;
    private int $quantite;

    /**
     * Constructeur
     */
    public function __construct(
        int     $id,
        string  $nom,
        ?string $description,
        float   $tarif,
        string  $categorie,
        ?string $imageUrl = null,
        int     $quantite = 1
    )
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->tarif = $tarif;
        $this->categorie = $categorie;
        $this->imageUrl = $imageUrl;
        $this->quantite = $quantite;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setTarif(float $tarif): void
    {
        $this->tarif = $tarif;
    }

    public function setCategorie(string $categorie): void
    {
        $this->categorie = $categorie;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function setQuantite(int $quantite): void
    {
        $this->quantite = $quantite;
    }
}
