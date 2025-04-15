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
<html lang="en">
<head>
    <meta charset="UTF~~@
    <title>Admin Dashboard - Fitsense</title>
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
    </style>
    <script>
        function showAddRow() {
            document.getElementById('add-row').style.display = 'table-row';
            document.getElementById('add-btn').style.display = 'none';
        }
        function hideAddRow() {
            document.getElementById('add-row').style.display = 'none';
            document.getElementById('add-btn').style.display = 'block';
            document.querySelector('form').reset();
        }
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
    <h1>Dashboard</h1>
    <div class="dashboard">
        <div class="card"><h3>Total Users</h3><p>50%</p></div>
        <div class="card"><h3>Total Products</h3><p>30%</p></div>
        <div class="card"><h3>Total Reservations</h3><p>40%</p></div>
        <div class="card"><h3>Total Reclamations</h3><p>10%</p></div>
    </div>

    <h2>Shop Products</h2>
    <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

    <button id="add-btn" class="add-btn" onclick="showAddRow()">Add Product</button>
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
                            <button type="button" class="edit-btn" onclick="window.location.href='modifier.php?id=<?php echo $produit['id']; ?>'">Edit</button>
                            <button type="button" class="delete-btn" onclick="if(confirm('Voulez-vous vraiment supprimer ce produit ?')) window.location.href='?action=delete&id=<?php echo $produit['id']; ?>'">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">Aucun produit trouvé.</td></tr>
            <?php endif; ?>

            <!-- Ligne d'ajout -->
            <tr id="add-row">
                <td><input type="number" name="id" min="1" required></td>
                <td>
                    <select name="nom" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($noms_possibles as $nom): ?>
                            <option value="<?php echo htmlspecialchars($nom); ?>"><?php echo htmlspecialchars($nom); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="number" name="prix" step="0.01" min="0" required></td>
                <td><input type="number" name="quantite" min="0" required></td>
                <td>
                    <select name="id_categorie" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo htmlspecialchars($categorie['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <button type="submit" name="confirm_add" class="confirm-btn">Confirm</button>
                    <button type="button" class="cancel-btn" onclick="hideAddRow()">Cancel</button>
                </td>
            </tr>
        </table>
    </form>

    <h2>Shop Details</h2>
    <table>
        <tr><th>Metric</th><th>Value</th></tr>
        <tr><td>Total Revenue</td><td>$5000</td></tr>
        <tr><td>Active Customers</td><td>120</td></tr>
        <tr><td>Pending Orders</td><td>15</td></tr>
    </table>
</div>
</body>

</html>