<?php
require_once __DIR__ . '/../config.php';
$pdo = config::getConnexion();

$error = null;
$success = null;

// Vérifier si un ID est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: back2.php?error=" . urlencode("ID du produit non valide."));
    exit;
}

$id = (int)$_GET['id'];

// Récupérer les données du produit
try {
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        header("Location: back2.php?error=" . urlencode("Produit non trouvé."));
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du produit : " . $e->getMessage();
    $produit = null;
}

// Récupérer les catégories pour le menu déroulant
try {
    $stmt = $pdo->query("SELECT id AS id_categorie, nom FROM categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des catégories : " . $e->getMessage();
    $categories = [];
}

// Récupérer les noms possibles pour le menu déroulant
try {
    $stmt = $pdo->query("SELECT DISTINCT nom FROM produit");
    $noms_possibles = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $noms_possibles = [];
}

// Gérer la soumission du formulaire pour mettre à jour le produit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_edit'])) {
    $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prix = filter_var($_POST['prix'] ?? 0, FILTER_VALIDATE_FLOAT);
    $quantite = filter_var($_POST['quantite'] ?? 0, FILTER_VALIDATE_INT);
    $id_categorie = filter_var($_POST['id_categorie'] ?? 0, FILTER_VALIDATE_INT);

    if (empty($nom) || $prix === false || $prix < 0 || $quantite === false || $quantite < 0 || $id_categorie === false) {
        $error = "Veuillez remplir tous les champs correctement.";
    } else {
        try {
            $sql = "UPDATE produit SET nom = :nom, prix = :prix, quantite = :quantite, id_categorie = :id_categorie WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prix' => $prix,
                ':quantite' => $quantite,
                ':id_categorie' => $id_categorie,
                ':id' => $id
            ]);

            if ($stmt->rowCount() > 0) {
                header("Location: back2.php?success=" . urlencode("Produit modifié avec succès !"));
            } else {
                $error = "Aucune modification effectuée. Vérifiez les données saisies.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification du produit : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit - Fitsense</title>
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
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .form-container h2 {
            margin-top: 0;
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .confirm-btn, .cancel-btn {
            background: #222;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }
        .confirm-btn:hover, .cancel-btn:hover {
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
    </style>
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
    <h1>Modifier un produit</h1>
    <div class="form-container">
        <?php if ($error): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>

        <?php if ($produit): ?>
            <form method="POST">
                <h2>Modifier le produit (ID: <?php echo htmlspecialchars($produit['id']); ?>)</h2>
                <label for="nom">Nom :</label>
                <select name="nom" id="nom" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($noms_possibles as $nom): ?>
                        <option value="<?php echo htmlspecialchars($nom); ?>" <?php if ($nom === $produit['nom']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($nom); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="prix">Prix :</label>
                <input type="number" name="prix" id="prix" step="0.01" min="0" value="<?php echo htmlspecialchars($produit['prix']); ?>" required>

                <label for="quantite">Quantité :</label>
                <input type="number" name="quantite" id="quantite" min="0" value="<?php echo htmlspecialchars($produit['quantite']); ?>" required>

                <label for="id_categorie">Catégorie :</label>
                <select name="id_categorie" id="id_categorie" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?php echo $categorie['id_categorie']; ?>" <?php if ($categorie['id_categorie'] == $produit['id_categorie']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($categorie['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="confirm_edit" class="confirm-btn">Confirmer</button>
                <a href="back2.php" class="cancel-btn">Annuler</a>
            </form>
        <?php else: ?>
            <p>Produit non trouvé.</p>
            <a href="back2.php" class="cancel-btn">Retour</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>