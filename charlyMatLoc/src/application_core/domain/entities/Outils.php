<?php


namespace charlyMatLoc\src\application_core\domain\entities;

class Outil
{
    private int $id;
    private string $nom;
    private ?string $description;
    private float $tarif;
    private string $categorie;

    /**
     * Constructeur
     */
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

    // --- Getters ---

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

    /**
     * Méthode utilitaire : convertir en tableau (utile pour les réponses JSON)
     */
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
