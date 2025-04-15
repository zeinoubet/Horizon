<?php
$siteTitle = "Fitsense - Club Multisports";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($siteTitle); ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
      background: url('https://images.unsplash.com/photo-1599058917212-d750089bc07e?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
      background-size: cover;
      display: flex;
    }

    /* Sidebar gauche (ajout du style de la sidebar du premier code) */
    .sidebar-left {
      width: 250px;
      height: 100vh;
      background-color: #222;
      padding-top: 40px;
      position: fixed;
      left: 0;
      top: 0;
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      z-index: 1001;
    }

    .sidebar-left h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #c5ff38;
    }

    .sidebar-left a {
      display: block;
      color: #ccc;
      padding: 12px 25px;
      text-decoration: none;
      transition: background 0.3s, color 0.3s;
    }

    .sidebar-left a:hover {
      background-color: #c5ff38;
      color: #000;
    }

    /* Navbar */
    .navbar {
      position: fixed;
      top: 0;
      left: 250px;
      width: calc(100% - 250px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background: rgba(0, 0, 0, 0.7);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      transition: background-color 0.3s ease;
    }

    .navbar:hover {
      background: rgba(0, 0, 0, 0.85);
    }

    /* Hero Section */
    .hero {
      margin-left: 250px;
      width: calc(100% - 250px);
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background: rgba(0, 0, 0, 0.6);
      padding: 20px;
      z-index: 1;
    }

    .hero h1 {
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 20px;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .hero .btn-group {
      display: flex;
      gap: 25px;
      justify-content: center;
    }

    .hero a {
      background: #c5ff38;
      color: #000;
      padding: 14px 30px;
      border-radius: 30px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      text-transform: uppercase;
      transition: background 0.3s, transform 0.3s;
    }

    .hero a:hover {
      background: #a8d82e;
      transform: translateY(-5px);
    }

    /* Footer */
    .footer {
      position: fixed;
      bottom: 0;
      left: 250px;
      width: calc(100% - 250px);
      background-color: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 10px 20px;
      text-align: center;
      font-size: 0.9rem;
      z-index: 1000;
    }

    .footer a {
      color: #c5ff38;
      text-decoration: none;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .sidebar-left {
        display: none;
      }

      .navbar {
        left: 0;
        width: 100%;
      }

      .hero {
        margin-left: 0;
        width: 100%;
      }

      .footer {
        left: 0;
        width: 100%;
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

  <!-- Barre latérale gauche -->
  <div class="sidebar-left">
    <h2>Navigation</h2>
    <a href="#">🏠 Accueil</a>
    <a href="#">🧘‍♀️ Activités</a>
    <a href="#">📅 Événements</a>
    <a href="front.php">🛍️ Magasin</a>
    <a href="#">📝 Forum</a>
    <a href="#">📞 Contact</a>
    <a href="#">⚙️ Gestion</a>
  </div>

  <!-- Section principale -->
  <section class="hero">
    <h1>Vivez le Sport Autrement</h1>
    <div class="btn-group">
      <a href="login.php">Se connecter</a>
      <a href="register.php">S'inscrire</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2025 Fitsense - Tous droits réservés. <a href="#">Mentions légales</a></p>
  </footer>

</body>
</html>
