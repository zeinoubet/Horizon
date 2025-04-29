<?php
$siteTitle = "Fitsense - Club Multisports"; // Titre du site
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($siteTitle); ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <style>
    /* Réinitialisation des styles par défaut */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Style du corps de la page */
    body {
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
      background: url('https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
      background-size: cover;
      display: flex;
      flex-direction: column;
    }

    /* Style de la barre de navigation horizontale */
    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.7); /* Fond semi-transparent */
      color: white;
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1001;
      transition: background-color 0.3s ease;
    }

    /* Effet au survol de la barre de navigation */
    .navbar:hover {
      background-color: rgba(0, 0, 0, 0.85);
    }

    /* Style du logo Greenmove */
    .navbar .logo {
      font-size: 24px;
      font-weight: bold;
      color: #c5ff38; /* Couleur verte */
    }

    /* Style du menu de navigation */
    .navbar .menu {
      display: flex;
      gap: 15px;
    }

    /* Style des liens du menu */
    .navbar .menu a {
      color: white;
      text-decoration: none;
      font-size: 18px;
      padding: 10px 15px;
      transition: color 0.3s;
    }

    /* Effet au survol des liens */
    .navbar .menu a:hover {
      color: #c5ff38;
    }

    /* Style de la section principale (hero) */
    .hero {
      margin-top: 80px; /* Espace pour la barre de navigation fixe */
      width: 100%;
      height: calc(100vh - 80px);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: rgba(0, 0, 0, 0.6); /* Fond semi-transparent */
      padding: 20px;
      z-index: 1;
    }

    /* Style du titre principal */
    .hero h1 {
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    /* Style du groupe de boutons */
    .hero .btn-group {
      display: flex;
      gap: 25px;
      justify-content: center;
    }

    /* Style des boutons de la section hero */
    .hero a {
      background: #c5ff38; /* Fond vert */
      color: #000;
      padding: 14px 30px;
      border-radius: 30px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      text-transform: uppercase;
      transition: background 0.3s, transform 0.3s;
    }

    /* Effet au survol des boutons */
    .hero a:hover {
      background: #a8d82e; /* Vert plus foncé */
      transform: translateY(-5px);
    }

    /* Style du pied de page */
    .footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 10px 20px;
      text-align: center;
      font-size: 0.9rem;
      z-index: 1000;
    }

    /* Style des liens du pied de page */
    .footer a {
      color: #c5ff38;
      text-decoration: none;
    }

    /* Effet au survol des liens du pied de page */
    .footer a:hover {
      text-decoration: underline;
    }

    /* Styles pour écrans plus petits (responsive) */
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        padding: 15px 20px;
      }

      .navbar .logo {
        margin-bottom: 10px;
      }

      .navbar .menu {
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }

      .navbar .menu a {
        font-size: 16px;
        padding: 8px 10px;
      }

      .hero {
        margin-top: 120px; /* Ajustement pour la barre plus haute */
        height: calc(100vh - 120px);
      }

      .hero h1 {
        font-size: 2.5rem;
      }

      .hero .btn-group a {
        padding: 12px 25px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Barre de navigation horizontale -->
  <nav class="navbar">
    <div class="logo">Greenmove</div>
    <div class="menu">
      <a href="#">Accueil</a>
      <a href="#">Activités</a>
      <a href="#">Événements</a>
      <a href="front.php">Magasin</a>
      <a href="#">Forum</a>
    </div>
  </nav>

  <!-- Section principale -->
  <section class="hero">
    <h1>Vivez le Sport Autrement</h1>
    <div class="btn-group">
      <a href="login.php">Se connecter</a>
      <a href="register.php">S'inscrire</a>
    </div>
  </section>

  <!-- Pied de page -->
  <footer class="footer">
    <p>© 2025 Fitsense - Tous droits réservés. <a href="#">Mentions légales</a></p>
  </footer>

</body>
</html>