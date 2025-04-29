<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=greenmove', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id'])) {
    header("Location: front.php");
    exit;
}

$commande_id = intval($_GET['id']);

// Vérifier que la commande existe et peut être annulée
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$commande_id]);
$commande = $stmt->fetch();

if (!$commande) {
    $_SESSION['message'] = "Commande introuvable";
    header("Location: front.php");
    exit;
}

// Supprimer la commande
$pdo->prepare("DELETE FROM commandes WHERE id = ?")->execute([$commande_id]);

// Message de confirmation
$_SESSION['message'] = "La commande #".$commande_id." a été annulée avec succès";
header("Location: front.php");
exit;
?>