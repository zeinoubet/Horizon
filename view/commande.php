<?php
// Connection à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=greenmove', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Traitement du formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $id_produit = intval($_POST['id_produit']);
    $nom_client = htmlspecialchars($_POST['nom_client']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $quantite = intval($_POST['quantite']);
    $taille = isset($_POST['taille']) ? strtoupper(htmlspecialchars($_POST['taille'])) : '';

    // Validation côté serveur de la taille
    $tailles_valides = ['S', 'M', 'L', 'XL', 'XXL'];
    if (!in_array($taille, $tailles_valides)) {
        die("Taille invalide.");
    }

    // Récupérer les infos du produit
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
    $stmt->execute([$id_produit]);
    $produit = $stmt->fetch();

    if (!$produit) {
        die("Produit introuvable.");
    }

    // Calculer le total
    $total = $produit['prix'] * $quantite;

    // Afficher la confirmation de commande
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Confirmation de commande</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: "Poppins", sans-serif; background-color: #f4f6f9; }
            .container { max-width: 800px; margin: 50px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
            h1 { color: #333; margin-bottom: 20px; }
            .confirmation { margin-top: 30px; }
            .confirmation p { margin: 10px 0; }
            .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #c5ff38; color: #000; text-decoration: none; border-radius: 8px; font-weight: 600; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Commande confirmée !</h1>
            <div class="confirmation">
                <p><strong>Produit :</strong> '.htmlspecialchars($produit['nom']).'</p>
                <p><strong>Prix unitaire :</strong> '.htmlspecialchars($produit['prix']).' €</p>
                <p><strong>Taille :</strong> '.$taille.'</p>
                <p><strong>Quantité :</strong> '.$quantite.'</p>
                <p><strong>Total :</strong> '.number_format($total, 2).' €</p>
                <p><strong>Nom du client :</strong> '.$nom_client.'</p>
                <p><strong>Adresse de livraison :</strong> '.nl2br($adresse).'</p>
            </div>
            <a href="front.php" class="btn">Retour à la boutique</a>
        </div>
    </body>
    </html>';
    exit;
}

// Vérifier si un id est passé en GET
if (!isset($_GET['id'])) {
    die("Produit non spécifié.");
}

$id = intval($_GET['id']);

// Récupérer les infos du produit
$stmt = $pdo->prepare("SELECT * FROM produit WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    die("Produit introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Passer une commande</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            background-color: #f4f6f9;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #222;
            color: #fff;
            padding-top: 40px;
            position: fixed;
            top: 0; left: 0; height: 100%;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #ccc;
            padding: 12px 25px;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }
        .sidebar a:hover {
            background-color: #c5ff38;
            color: #000;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px 30px;
            width: calc(100% - 250px);
        }
        h1 {
            margin-bottom: 30px;
            color: #333;
        }
        form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #333;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        form button {
            margin-top: 20px;
            background: #c5ff38;
            border: none;
            padding: 12px 20px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background: #aee636;
        }
        .readonly {
            background-color: #eee;
        }
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>

<div class="sidebar">
  <h2>Navigation</h2>
  <a href="front.php">🏠 Retour Boutique</a>
</div>

<div class="main-content">
  <h1>Passer une Commande</h1>

  <form action="commande.php" method="POST" onsubmit="return validateForm()">
    <input type="hidden" name="id_produit" value="<?php echo htmlspecialchars($produit['id']); ?>">

    <label>Produit :</label>
    <input type="text" class="readonly" value="<?php echo htmlspecialchars($produit['nom']); ?>" readonly>

    <label>Prix unitaire (€) :</label>
    <input type="text" class="readonly" value="<?php echo htmlspecialchars($produit['prix']); ?>" readonly>

    <label for="taille">Taille :</label>
    <input type="text" 
           name="taille" 
           id="taille" 
           placeholder="S, M, L, XL, XXL" 
           oninput="validateSize()"
           required>
    <div id="size-error" class="error-message">Veuillez entrer une taille valide (S, M, L, XL, XXL)</div>

    <label for="nom_client">Votre nom :</label>
    <input type="text" name="nom_client" id="nom_client" required>

    <label for="adresse">Votre adresse :</label>
    <textarea name="adresse" id="adresse" required></textarea>

    <label for="quantite">Quantité :</label>
    <input type="number" name="quantite" id="quantite" min="1" required>

    <button type="submit">Confirmer la commande</button>
  </form>
</div>

<script>
    function validateSize() {
        const tailleInput = document.getElementById('taille');
        const errorElement = document.getElementById('size-error');
        const taille = tailleInput.value.toUpperCase();
        const validSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        
        if (validSizes.includes(taille)) {
            tailleInput.value = taille; // Met en majuscules
            errorElement.style.display = 'none';
            return true;
        } else {
            errorElement.style.display = 'block';
            return false;
        }
    }

    function validateForm() {
        const isSizeValid = validateSize();
        
        if (!isSizeValid) {
            alert('Veuillez choisir une taille valide (S, M, L, XL, XXL)');
            return false;
        }
        
        return true;
    }

    // Convertit automatiquement en majuscules
    document.getElementById('taille').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>

</body>
</html>