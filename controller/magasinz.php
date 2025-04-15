<?php
require_once __DIR__ . '/../model/magasin.php';

class MagasinzController {
    private $model;
    private $produit_categories = [
        'Pantalon cargo' => 1,
        'T-shirt blanc' => 2,
        'T-shirt noir' => 3,
        'Jean bleu' => 4,
        'Hoodie gris' => 5
    ];

    public function __construct() {
        $this->model = new Magasin();
    }

    public function ajouterProduit() {
        $error = null;
        $success = null;
        $produits = [];

        // Récupérer les produits existants
        try {
            $produits = $this->model->getProduits();
        } catch (Exception $e) {
            $error = "Erreur lors de la récupération des produits : " . $e->getMessage();
        }

        // Liste des noms de produits disponibles
        $available_names = array_keys($this->produit_categories);
        // Exclure les noms déjà utilisés
        foreach ($produits as $produit) {
            if (($key = array_search($produit['nom'], $available_names)) !== false) {
                unset($available_names[$key]);
            }
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérer et valider les données
            $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
            $prix = filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
            $quantite = filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT);

            // Vérification des champs
            if (!array_key_exists($nom, $this->produit_categories)) {
                $error = "Nom de produit non autorisé. Choisissez parmi : " . implode(', ', array_keys($this->produit_categories)) . ".";
            } elseif (empty($nom) || $prix === false || $prix < 0 || $quantite === false || $quantite < 0) {
                $error = "Veuillez remplir tous les champs correctement.";
            } elseif ($this->model->produitExistsByName($nom)) {
                $error = "Un produit avec ce nom existe déjà.";
            } else {
                try {
                    $id_categorie = $this->produit_categories[$nom];
                    $this->model->addProduit($nom, $prix, $quantite, $id_categorie);
                    $success = "Produit ajouté avec succès !";
                    $produits = $this->model->getProduits();
                    // Mettre à jour les noms disponibles
                    $available_names = array_keys($this->produit_categories);
                    foreach ($produits as $produit) {
                        if (($key = array_search($produit['nom'], $available_names)) !== false) {
                            unset($available_names[$key]);
                        }
                    }
                } catch (Exception $e) {
                    $error = "Erreur lors de l'ajout du produit : " . $e->getMessage();
                }
            }
        }

        $produit_categories = $this->produit_categories;

        // Charger la vue avec les données
        require_once __DIR__ . '/../view/ajout.php';
    }

    // Affiche tous les produits
    public function index() {
        try {
            $produits = $this->model->getProduits();
            require_once __DIR__ . '/../view/back2.php';
        } catch (Exception $e) {
            $error = "Erreur lors de la récupération des produits : " . $e->getMessage();
            require_once __DIR__ . '/../view/back2.php';
        }
    }

    // Supprime un produit
    public function delete($id) {
        try {
            // Valider l'ID
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(400);
                $error = "ID du produit non valide.";
                require_once __DIR__ . '/../view/back2.php';
                return;
            }

            $id = (int)$id;

            // Vérifier si le produit existe
            $checkStmt = $this->model->getPdo()->prepare("SELECT COUNT(*) FROM produit WHERE id = :id");
            $checkStmt->execute(['id' => $id]);
            
            if ($checkStmt->fetchColumn() == 0) {
                http_response_code(404);
                $error = "Produit non trouvé.";
                require_once __DIR__ . '/../view/back2.php';
                return;
            }

            // Supprimer le produit
            $stmt = $this->model->getPdo()->prepare("DELETE FROM produit WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() > 0) {
                $success = "Produit supprimé avec succès !";
                http_response_code(200);
            } else {
                $error = "Erreur lors de la suppression du produit.";
                http_response_code(500);
            }

        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
            http_response_code(500);
        } catch (Exception $e) {
            $error = "Erreur : " . $e->getMessage();
            http_response_code(500);
        }

        // Recharger la liste des produits et afficher la vue
        $produits = $this->model->getProduits();
        require_once __DIR__ . '/../view/back2.php';
    }
}

// Gestion des routes
try {
    $controller = new MagasinzController();
    
    // Déterminer l'action à exécuter
    $action = $_GET['action'] ?? 'index';
    $id = $_GET['id'] ?? null;

    switch ($action) {
        case 'ajouter':
            $controller->ajouterProduit();
            break;
        case 'delete':
            if ($id !== null) {
                $controller->delete($id);
            } else {
                $error = "ID du produit non spécifié.";
                $produits = $controller->model->getProduits();
                require_once __DIR__ . '/../view/back2.php';
            }
            break;
        case 'index':
        default:
            $controller->index();
            break;
    }
} catch (Exception $e) {
    $error = "Erreur : " . $e->getMessage();
    require_once __DIR__ . '/../view/back2.php';
} 
// Affiche le formulaire de modification
public function edit($id) {
    try {
        $produit = $this->model->getProduitById($id);
        if (!$produit) {
            $error = "Produit non trouvé.";
            $produits = $this->model->getProduits();
            require_once __DIR__ . '/../view/back2.php';
            return;
        }

        // Récupérer les catégories et les noms possibles
        $stmt = $this->model->getPdo()->query("SELECT id AS id_categorie, nom FROM categorie");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->model->getPdo()->query("SELECT DISTINCT nom FROM produit");
        $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);

        require_once __DIR__ . '/../view/edit_product.php';
    } catch (Exception $e) {
        $error = "Erreur lors de la récupération du produit : " . $e->getMessage();
        $produits = $this->model->getProduits();
        require_once __DIR__ . '/../view/back2.php';
    }
}

// Met à jour le produit
public function update($id) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
        $prix = filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
        $quantite = filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT);
        $id_categorie = filter_var($_POST['id_categorie'] ?? 0, FILTER_VALIDATE_INT);

        if (empty($nom) || $prix === false || $prix < 0 || $quantite === false || $quantite < 0 || $id_categorie === false) {
            $error = "Veuillez remplir tous les champs correctement.";
            $produit = $this->model->getProduitById($id);
            $stmt = $this->model->getPdo()->query("SELECT id AS id_categorie, nom FROM categorie");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $this->model->getPdo()->query("SELECT DISTINCT nom FROM produit");
            $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
            require_once __DIR__ . '/../view/edit_product.php';
        } else {
            try {
                $this->model->updateProduit($id, $nom, $prix, $quantite, $id_categorie);
                $success = "Produit modifié avec succès !";
                $produits = $this->model->getProduits();
                require_once __DIR__ . '/../view/back2.php';
            } catch (Exception $e) {
                $error = "Erreur lors de la modification du produit : " . $e->getMessage();
                $produit = $this->model->getProduitById($id);
                $stmt = $this->model->getPdo()->query("SELECT id AS id_categorie, nom FROM categorie");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = $this->model->getPdo()->query("SELECT DISTINCT nom FROM produit");
                $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
                require_once __DIR__ . '/../view/edit_product.php';
            }
        }
    }
}

?>