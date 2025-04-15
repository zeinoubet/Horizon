<?php
require_once __DIR__ . '/../config.php';

try {
    // Récupérer la connexion à la base de données
    $pdo = config::getConnexion();
    
    // Vérifier si l'ID du produit est passé en paramètre
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: back2.php?error=" . urlencode("ID du produit non valide."));
        exit;
    }

    $id = (int)$_GET['id'];

    // Vérifier si le produit existe avant de supprimer
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE id = :id");
    $checkStmt->execute(['id' => $id]);
    
    if ($checkStmt->fetchColumn() == 0) {
        header("Location: back2.php?error=" . urlencode("Produit non trouvé."));
        exit;
    }

    // Supprimer le produit
    $stmt = $pdo->prepare("DELETE FROM produit WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    // Vérifier si la suppression a réussi
    if ($stmt->rowCount() > 0) {
        header("Location: back2.php?success=" . urlencode("Produit supprimé avec succès !"));
    } else {
        header("Location: back2.php?error=" . urlencode("Erreur lors de la suppression du produit."));
    }

} catch (PDOException $e) {
    header("Location: back2.php?error=" . urlencode("Erreur de base de données : " . $e->getMessage()));
} catch (Exception $e) {
    header("Location: back2.php?error=" . urlencode("Erreur : " . $e->getMessage()));
}
exit;
?>