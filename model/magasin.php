<?php

class Produit {
    private $id;
    private $nom;
    private $description;
    private $prix;
    private $categorie;
    

    // Constructeur pour initialiser les propriétés
    public function __construct($nom = '', $description = '', $prix = 0, $categorie = '') {
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->categorie = $categorie;
        
    }

    // Getter pour id
    public function getId() {
        return $this->id;
    }

    // Setter pour id
    public function setId($id) {
        $this->id = $id;
    }

    // Getter pour nom
    public function getNom() {
        return $this->nom;
    }

    // Setter pour nom
    public function setNom($nom) {
        $this->nom = $nom;
    }

    // Getter pour description
    public function getDescription() {
        return $this->description;
    }

    // Setter pour description
    public function setDescription($description) {
        $this->description = $description;
    }

    // Getter pour prix
    public function getPrix() {
        return $this->prix;
    }

    // Setter pour prix
    public function setPrix($prix) {
        $this->prix = $prix;
    }

    // Getter pour categorie
    public function getCategorie() {
        return $this->categorie;
    }

    // Setter pour categorie
    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    
}



require_once __DIR__ . '/../config.php';


class Magasin {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    public function getProduits() {
        $stmt = $this->pdo->query("SELECT * FROM produit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDistinctNoms() {
        $stmt = $this->pdo->query("SELECT DISTINCT nom FROM produit");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function addProduit($nom, $prix, $quantite, $id_categorie) {
        $stmt = $this->pdo->prepare("INSERT INTO produit (nom, prix, quantite, id_categorie) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prix, $quantite, $id_categorie]);
    }
}

<?php
class MagasinModel {

    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=greenmove", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Récupère tous les produits
    public function getAllProduits() {
        $stmt = $this->pdo->query("SELECT * FROM produit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère un produit par son ID
    public function getProduitById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produit WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajoute un produit
    public function addProduit($nom, $prix, $quantite, $id_categorie) {
        $stmt = $this->pdo->prepare("INSERT INTO produit (nom, prix, quantite, id_categorie) VALUES (:nom, :prix, :quantite, :id_categorie)");
        $stmt->execute([
            ':nom' => $nom,
            ':prix' => $prix,
            ':quantite' => $quantite,
            ':id_categorie' => $id_categorie
        ]);
    }

    // Met à jour un produit
    public function updateProduit($id, $nom, $prix, $quantite, $id_categorie) {
        $stmt = $this->pdo->prepare("UPDATE produit SET nom = :nom, prix = :prix, quantite = :quantite, id_categorie = :id_categorie WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':prix' => $prix,
            ':quantite' => $quantite,
            ':id_categorie' => $id_categorie
        ]);
    }

    // Supprime un produit
    public function deleteProduit($id) {
        $stmt = $this->pdo->prepare("DELETE FROM produit WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    public function produitExistsByName($nom) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM produit WHERE nom = :nom");
        $stmt->execute(['nom' => $nom]);
        return $stmt->fetchColumn() > 0;
    }
}
?>


<?php