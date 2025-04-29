<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=greenmove', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id'])) {
    header("Location: front.php");
    exit;
}

$commande_id = intval($_GET['id']);

// Récupérer la commande
$stmt = $pdo->prepare("SELECT c.*, p.nom as produit_nom, p.prix as produit_prix 
                       FROM commandes c 
                       JOIN produit p ON c.id_produit = p.id 
                       WHERE c.id = ?");
$stmt->execute([$commande_id]);
$commande = $stmt->fetch();

if (!$commande) {
    $_SESSION['message'] = "Commande introuvable";
    header("Location: front.php");
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les nouvelles données
    $nom_client = htmlspecialchars($_POST['nom_client']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $quantite = intval($_POST['quantite']);
    $taille = strtoupper(htmlspecialchars($_POST['taille']));
    
    // Valider la taille
    $tailles_valides = ['S', 'M', 'L', 'XL', 'XXL'];
    if (!in_array($taille, $tailles_valides)) {
        die("Taille invalide.");
    }
    
    // Calculer le nouveau total
    $total = $commande['produit_prix'] * $quantite;
    
    // Mettre à jour la commande
    $stmt = $pdo->prepare("UPDATE commandes SET nom_client = ?, adresse = ?, quantite = ?, taille = ?, total = ? WHERE id = ?");
    $stmt->execute([$nom_client, $adresse, $quantite, $taille, $total, $commande_id]);
    
    $_SESSION['message'] = "La commande #".$commande_id." a été modifiée avec succès";
    header("Location: commande.php?id=".$commande['id_produit']);
    exit;
}

// Afficher le formulaire de modification
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la commande</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Reprenez le même style que dans commande.php */
    </style>
</head>
<body>

<div class="sidebar">
  <h2>Navigation</h2>
  <a href="front.php">🏠 Retour Boutique</a>
</div>

<div class="main-content">
  <h1>Modifier la Commande #<?php echo $commande_id; ?></h1>

  <form action="modifier_commande.php?id=<?php echo $commande_id; ?>" method="POST">
    <input type="hidden" name="id_produit" value="<?php echo htmlspecialchars($commande['id_produit']); ?>">

    <label>Produit :</label>
    <input type="text" class="readonly" value="<?php echo htmlspecialchars($commande['produit_nom']); ?>" readonly>

    <label>Prix unitaire (€) :</label>
    <input type="text" class="readonly" value="<?php echo htmlspecialchars($commande['produit_prix']); ?>" readonly>

    <label for="taille">Taille :</label>
    <input type="text" 
           name="taille" 
           id="taille" 
           value="<?php echo htmlspecialchars($commande['taille']); ?>"
           placeholder="S, M, L, XL, XXL"
           required>
    <div id="tailleError" class="error">Veuillez choisir une taille valide (S, M, L, XL, XXL)</div>

    <label for="nom_client">Votre nom :</label>
    <input type="text" name="nom_client" id="nom_client" value="<?php echo htmlspecialchars($commande['nom_client']); ?>" required>

    <label for="adresse">Votre adresse :</label>
    <textarea name="adresse" id="adresse" required><?php echo htmlspecialchars($commande['adresse']); ?></textarea>

    <label for="quantite">Quantité :</label>
    <input type="number" name="quantite" id="quantite" min="1" value="<?php echo htmlspecialchars($commande['quantite']); ?>" required>

    <button type="submit">Enregistrer les modifications</button>
  </form>
</div>

<script>
    // Reprenez le même script de validation que dans commande.php
</script>

</body>
</html>