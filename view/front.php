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
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #222;
      color: #fff;
      padding-top: 40px;
      position: fixed;
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
      padding: 40px;
      width: calc(100% - 250px);
    }

    h1 {
      margin-bottom: 20px;
      color: #333;
    }

    .categories {
      display: flex;
      gap: 15px;
      margin-bottom: 30px;
    }

    .categories button {
      background: #c5ff38;
      color: #000;
      border: none;
      padding: 10px 18px;
      border-radius: 6px;
      cursor: pointer;
    }

    .categories button:hover {
      background: #aee636;
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 20px;
    }

    .product-card {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }

    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 5px;
    }

    .product-card h3 {
      margin: 10px 0 5px;
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
    <h1>Nos Produits</h1>

    <div class="categories">
      <button onclick="filterProducts('all')">Tous</button>
      <button onclick="filterProducts('tshirt')">T-Shirts</button>
      <button onclick="filterProducts('hoodie')">Hoodies</button>
      <button onclick="filterProducts('pants')">Pants</button>
    </div>

    <div class="products">
      <div class="product-card" data-category="tshirt">
        <img src="t-shirtN.webp" alt="T-Shirt">
        <h3>T-Shirt Noir</h3>
      </div>

      <div class="product-card" data-category="hoodie">
        <img src="h.webp" alt="Hoodie">
        <h3>Hoodie Gris</h3>
      </div>

      <div class="product-card" data-category="pants">
        <img src="pantsC.png" alt="Pants">
        <h3>Pantalon Cargo</h3>
      </div>

      <div class="product-card" data-category="tshirt">
        <img src="t-shirt.webp" alt="T-Shirt">
        <h3>T-Shirt Blanc</h3>
      </div>

      <div class="product-card" data-category="pants">
        <img src="jean.webp " alt="Pants">
        <h3>Jean Bleu</h3>
      </div>
    </div>
  </div>

  <script>
    function filterProducts(category) {
      const cards = document.querySelectorAll('.product-card');
      cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>
