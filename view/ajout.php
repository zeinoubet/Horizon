<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 20px;
        }
        h1, h2 {
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #c5ff38;
            color: black;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #aee636;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
        .success {
            color: green;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
    </style>
</head>
<body>
    <h1>Ajouter un produit</h1>

    <?php if ($error): ?>
        <p class='error'><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class='success'><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="nom">Nom du produit</label>
        <select id="nom" name="nom" required>
            <option value="">Sélectionner un produit</option>
            <?php foreach ($available_names as $name): ?>
                <option value="<?php echo htmlspecialchars($name); ?>">
                    <?php echo htmlspecialchars($name); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="prix">Prix</label>
        <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?php echo isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : ''; ?>" required>

        <label for="quantite">Quantité</label>
        <input type="number" id="quantite" name="quantite" min="0" value="<?php echo isset($_POST['quantite']) ? htmlspecialchars($_POST['quantite']) : ''; ?>" required>

        <button type="submit">Ajouter</button>
    </form>

    <h2>Produits Existants</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Catégorie</th>
        </tr>
        <?php if (!empty($produits)): ?>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['id']); ?></td>
                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td><?php echo htmlspecialchars($produit['prix']); ?></td>
                    <td><?php echo htmlspecialchars($produit['quantite']); ?></td>
                    <td><?php echo htmlspecialchars(array_search($produit['id_categorie'], $produit_categories) ?: 'Inconnue'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Aucun produit trouvé.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>