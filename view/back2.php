<?php
require_once __DIR__ . '/../config.php';
$pdo = config::getConnexion();

$error = null;
$success = null;

// Récupérer les produits
try {
    $stmt = $pdo->query("SELECT * FROM produit");
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des produits : " . $e->getMessage();
    $produits = [];
}

// Récupérer les catégories
try {
    $stmt = $pdo->query("SELECT id AS id_categorie, nom FROM categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
    $categories = [];
}

// Récupérer les noms possibles
try {
    $stmt = $pdo->query("SELECT DISTINCT nom FROM produit");
    $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $noms_possibles = [];
}

// Gestion de l'ajout d'un produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_add'])) {
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prix = filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
    $quantite = filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT);
    $id_categorie = filter_var($_POST['id_categorie'] ?? 0, FILTER_VALIDATE_INT);

    if ($id === false || $id < 1 || empty($nom) || $prix === false || $prix < 0 || $quantite === false || $quantite < 0 || $id_categorie === false) {
        $error = "Veuillez remplir tous les champs correctement.";
    } else {
        try {
            // Vérifier si l'id existe déjà (double vérification côté serveur)
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = "L'ID $id existe déjà. Veuillez choisir un autre ID.";
            } else {
                $sql = "INSERT INTO produit (id, nom, prix, quantite, id_categorie) 
                        VALUES (:id, :nom, :prix, :quantite, :id_categorie)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id' => $id,
                    ':nom' => $nom,
                    ':prix' => $prix,
                    ':quantite' => $quantite,
                    ':id_categorie' => $id_categorie
                ]);
                $success = "Produit ajouté avec succès !";
                // Rafraîchir la liste des produits
                $stmt = $pdo->query("SELECT * FROM produit");
                $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Mettre à jour les noms possibles
                $stmt = $pdo->query("SELECT DISTINCT nom FROM produit");
                $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du produit : " . $e->getMessage();
        }
    }
}

// Gestion de la suppression d'un produit
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false || $id < 1) {
        $error = "ID du produit non valide.";
    } else {
        try {
            // Vérifier si le produit existe
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE id = :id");
            $checkStmt->execute(['id' => $id]);
            
            if ($checkStmt->fetchColumn() == 0) {
                $error = "Produit non trouvé.";
            } else {
                // Supprimer le produit
                $stmt = $pdo->prepare("DELETE FROM produit WHERE id = :id");
                $stmt->execute(['id' => $id]);
                
                if ($stmt->rowCount() > 0) {
                    $success = "Produit supprimé avec succès !";
                    // Rafraîchir la liste des produits
                    $stmt = $pdo->query("SELECT * FROM produit");
                    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Mettre à jour les noms possibles
                    $stmt = $pdo->query("SELECT DISTINCT nom FROM produit");
                    $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
                } else {
                    $error = "Erreur lors de la suppression du produit.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Admin - Fitsense</title>
    <link rel="stylesheet" href="styles.css">                                      
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #c5ff38;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #222;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: white;
            color: black;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .dashboard {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 200px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .add-btn, .confirm-btn, .cancel-btn, .edit-btn, .delete-btn {
            background: #222;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }
        .add-btn:hover, .confirm-btn:hover, .cancel-btn:hover, .edit-btn:hover, .delete-btn:hover {
            background: #444;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        #add-row {
            display: none;
        }
        #add-row input, #add-row select {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
            display: none;
        }
    </style>
    <script>
        let idExists = false;

        function checkId() {
            const idInput = document.querySelector('input[name="id"]');
            const idError = document.getElementById('id-error');
            const idValue = idInput.value.trim();

            if (idValue === '') {
                idError.textContent = 'L\'ID est requis.';
                idError.style.display = 'block';
                idExists = false;
                return;
            }

            const id = parseInt(idValue);
            if (isNaN(id) || id < 1) {
                idError.textContent = 'L\'ID doit être un entier positif supérieur ou égal à 1.';
                idError.style.display = 'block';
                idExists = false;
                return;
            }

            // Requête AJAX pour vérifier si l'ID existe
            fetch('check_id.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        idError.textContent = data.error;
                        idError.style.display = 'block';
                        idExists = false;
                    } else if (data.exists) {
                        idError.textContent = 'Cet ID existe déjà. Veuillez choisir un autre ID.';
                        idError.style.display = 'block';
                        idExists = true;
                    } else {
                        idError.style.display = 'none';
                        idExists = false;
                    }
                })
                .catch(error => {
                    idError.textContent = 'Erreur lors de la vérification de l\'ID.';
                    idError.style.display = 'block';
                    idExists = false;
                });
        }

        function showAddRow() {
            document.getElementById('add-row').style.display = 'table-row';
            document.getElementById('add-btn').style.display = 'none';
            // Réinitialiser les messages d'erreur
            document.querySelectorAll('.error-message').forEach(function(el) {
                el.style.display = 'none';
            });
        }

        function hideAddRow() {
            document.getElementById('add-row').style.display = 'none';
            document.getElementById('add-btn').style.display = 'block';
            document.querySelectorAll('.error-message').forEach(function(el) {
                el.style.display = 'none';
            });
            document.querySelector('form').reset();
        }

        function validateForm() {
            let isValid = true;

            // Validation ID
            const idInput = document.querySelector('input[name="id"]');
            const idError = document.getElementById('id-error');
            const idValue = idInput.value.trim();
            if (idValue === '') {
                idError.textContent = 'L\'ID est requis.';
                idError.style.display = 'block';
                isValid = false;
            } else {
                const id = parseInt(idValue);
                if (isNaN(id) || id < 1) {
                    idError.textContent = 'L\'ID doit être un entier positif supérieur ou égal à 1.';
                    idError.style.display = 'block';
                    isValid = false;
                } else if (idExists) {
                    idError.textContent = 'Cet ID existe déjà. Veuillez choisir un autre ID.';
                    idError.style.display = 'block';
                    isValid = false;
                } else {
                    idError.style.display = 'none';
                }
            }

            // Validation Nom
            const nomSelect = document.querySelector('select[name="nom"]');
            const nomError = document.getElementById('nom-error');
            if (nomSelect.value === '') {
                nomError.textContent = 'Veuillez sélectionner un nom.';
                nomError.style.display = 'block';
                isValid = false;
            } else {
                nomError.style.display = 'none';
            }

            // Validation Prix
            const prixInput = document.querySelector('input[name="prix"]');
            const prixError = document.getElementById('prix-error');
            const prixValue = prixInput.value.trim();
            if (prixValue === '') {
                prixError.textContent = 'Le prix est requis.';
                prixError.style.display = 'block';
                isValid = false;
            } else {
                const prix = parseFloat(prixValue);
                if (isNaN(prix) || prix < 0) {
                    prixError.textContent = 'Le prix doit être un nombre positif.';
                    prixError.style.display = 'block';
                    isValid = false;
                } else {
                    prixError.style.display = 'none';
                }
            }

            // Validation Quantité
            const quantiteInput = document.querySelector('input[name="quantite"]');
            const quantiteError = document.getElementById('quantite-error');
            const quantiteValue = quantiteInput.value.trim();
            if (quantiteValue === '') {
                quantiteError.textContent = 'La quantité est requise.';
                quantiteError.style.display = 'block';
                isValid = false;
            } else {
                const quantite = parseInt(quantiteValue);
                if (isNaN(quantite) || quantite < 0) {
                    quantiteError.textContent = 'La quantité doit être un entier positif ou zéro.';
                    quantiteError.style.display = 'block';
                    isValid = false;
                } else {
                    quantiteError.style.display = 'none';
                }
            }

            // Validation ID Catégorie
            const categorieSelect = document.querySelector('select[name="id_categorie"]');
            const categorieError = document.getElementById('categorie-error');
            if (categorieSelect.value === '') {
                categorieError.textContent = 'Veuillez sélectionner une catégorie.';
                categorieError.style.display = 'block';
                isValid = false;
            } else {
                categorieError.style.display = 'none';
            }

            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!validateForm()) {
                    event.preventDefault(); // Empêche la soumission du formulaire
                }
            });

            // Ajouter un écouteur d'événements pour vérifier l'ID à chaque changement
            const idInput = document.querySelector('input[name="id"]');
            idInput.addEventListener('input', checkId);
        });
    </script>
</head>
<body>
<div class="sidebar">
    <h2>Navigation</h2>
    <a href="#">🏠 Accueil</a>
    <a href="#">🧘‍♀️ Activités</a>
    <a href="#">📅 Événements</a>
    <a href="back2.php">🛍️ Magasin</a>
    <a href="#">📝 Forum</a>
    <a href="#">📞 Contact</a>
    <a href="#">⚙️ Gestion</a>
</div>

<div class="main-content">
    <h1>Tableau de Bord</h1>
    <div class="dashboard">
        <div class="card"><h3>Utilisateurs totaux</h3><p>50%</p></div>
        <div class="card"><h3>Produits totaux</h3><p>30%</p></div>
        <div class="card"><h3>Réservations totales</h3><p>40%</p></div>
        <div class="card"><h3>Réclamations totales</h3><p>10%</p></div>
    </div>

    <h2>Produits du Magasin</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <button id="add-btn" class="add-btn" onclick="showAddRow()">Ajouter un Produit</button>
    <form method="POST">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>ID Catégorie</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($produits)): ?>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produit['id']); ?></td>
                        <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                        <td><?php echo htmlspecialchars($produit['prix']); ?></td>
                        <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                        <td><?php echo htmlspecialchars($produit['id_categorie']); ?></td>
                        <td>
                            <button type="button" class="edit-btn" onclick="window.location.href='modifier.php?id=<?php echo $produit['id']; ?>'">Modifier</button>
                            <button type="button" class="delete-btn" onclick="if(confirm('Voulez-vous vraiment supprimer ce produit ?')) window.location.href='?action=delete&id=<?php echo $produit['id']; ?>'">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Aucun produit trouvé.</td></tr>
            <?php endif; ?>

            <!-- Ligne d'ajout -->
            <tr id="add-row">
                <td>
                    <input type="text" name="id" id="id">
                    <span id="id-error" class="error-message"></span>
                </td>
                <td>
                    <select name="nom" id="nom">
                        <option value="">Sélectionner</option>
                        <?php foreach ($noms_possibles as $nom): ?>
                            <option value="<?php echo htmlspecialchars($nom); ?>"><?php echo htmlspecialchars($nom); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="nom-error" class="error-message"></span>
                </td>
                <td>
                    <input type="text" name="prix" id="prix">
                    <span id="prix-error" class="error-message"></span>
                </td>
                <td>
                    <input type="text" name="quantite" id="quantite">
                    <span id="quantite-error" class="error-message"></span>
                </td>
                <td>
                    <select name="id_categorie" id="id_categorie">
                        <option value="">Sélectionner</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo htmlspecialchars($categorie['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="categorie-error" class="error-message"></span>
                </td>
                <td>
                    <button type="submit" name="confirm_add" class="confirm-btn">Confirmer</button>
                    <button type="button" class="cancel-btn" onclick="hideAddRow()">Annuler</button>
                </td>
            </tr>
        </table>
    </form>

    <h2>Détails du Magasin</h2>
    <table>
        <tr><th>Métrique</th><th>Valeur</th></tr>
        <tr><td>Revenu total</td><td>5000 $</td></tr>
        <tr><td>Clients actifs</td><td>120</td></tr>
        <tr><td>Commandes en attente</td><td>15</td></tr>
    </table>
</div>
</body>
</html>