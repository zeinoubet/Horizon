<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Boutique - Front Office</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
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
      top: 0;
      left: 0;
      height: 100%;
      overflow-y: auto;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
      font-size: 24px;
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
      font-size: 32px;
      font-weight: 600;
    }

    .categories {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }

    .categories button {
      background: #c5ff38;
      color: #000;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s, transform 0.2s;
    }

    .categories button:hover {
      background: #aee636;
      transform: scale(1.05);
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 25px;
    }

    .product-card {
      display: block;
      background: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
      opacity: 1;
      transform: scale(1);
      text-decoration: none;
      color: inherit;
      cursor: pointer;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .product-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
    }

    .product-card h3 {
      margin-top: 12px;
      font-size: 18px;
      color: #333;
      font-weight: 600;
    }

    .hidden {
      display: none;
      opacity: 0;
      transform: scale(0.95);
      transition: all 0.3s ease;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2>Navigation</h2>
    <a href="#">🏠 Accueil</a>
    <a href="#">🧘‍♀️ Activités</a>
    <a href="#">📅 Événements</a>
    <a href="#">🛍️ Magasin</a>
    <a href="#">📝 Forum</a>
    <a href="#">📞 Contact</a>
    <a href="#">⚙️ Gestion</a>
  </div>

  <div class="main-content">
    <h1>Nos Produits</h1>

    <div class="categories">
      <button onclick="filterProducts('all')">Tous</button>
      <button onclick="filterProducts('8')">T-Shirts</button>
      <button onclick="filterProducts('9')">Hoodies</button>
      <button onclick="filterProducts('13')">Pantalons</button>
    </div>

    <div class="products">
      <?php
      // Connection à la base de données
      $pdo = new PDO('mysql:host=localhost;dbname=greenmove', 'root', '');
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      try {
        $stmt = $pdo->query("SELECT * FROM produit");
        while ($produit = $stmt->fetch()) {
          $id = htmlspecialchars($produit['id']);
          $nom = htmlspecialchars($produit['nom']);
          $categorie = htmlspecialchars($produit['id_categorie']);
          $image = htmlspecialchars($produit['image']);
          
          // Correction du nom "Hoddie" en "Hoodie"
          $nom = str_replace('Hoddie', 'Hoodie', $nom);
          
          echo '<a href="commande.php?id=' . $id . '" class="product-card" data-category="' . $categorie . '">';
          
          // Chemin de l'image
          $imagePath = 'view/' . $image;
          
          // Vérification de l'existence de l'image
          if (file_exists($imagePath)) {
            echo '<img src="' . $imagePath . '" alt="' . $nom . '" loading="lazy">';
          } else {
            echo '<img src="view/default.webp" alt="Image non disponible" loading="lazy">';
          }
          
          echo '<h3>' . $nom . '</h3>';
          echo '</a>';
        }
      } catch (PDOException $e) {
        echo '<p>Erreur de chargement des produits: ' . htmlspecialchars($e->getMessage()) . '</p>';
      }
      ?>
    </div>
  </div>

  <script>
    function filterProducts(category) {
      const cards = document.querySelectorAll('.product-card');
      
      cards.forEach(card => {
        if (category === 'all') {
          card.classList.remove('hidden');
        } else {
          if (card.dataset.category === category) {
            card.classList.remove('hidden');
          } else {
            card.classList.add('hidden');
          }
        }
      });
    }

    // Filtre initial - affiche tous les produits
    document.addEventListener('DOMContentLoaded', function() {
      filterProducts('all');
    });
  </script>

</body>
</html>