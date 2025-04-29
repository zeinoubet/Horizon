<form method="POST">
    <label for="client_nom">Nom du Client :</label>
    <input type="text" id="client_nom" name="client_nom" required>

    <label for="quantite">Quantité :</label>
    <input type="number" id="quantite" name="quantite" required>

    <label for="adresse">Adresse :</label>
    <input type="text" id="adresse" name="adresse" required>

    <label for="id_produit">Produit :</label>
    <select id="id_produit" name="id_produit" required>
        <?php foreach ($produits as $produit) { ?>
            <option value="<?= $produit['id'] ?>"><?= $produit['nom'] ?></option>
        <?php } ?>
    </select>

    <button type="submit">Ajouter la commande</button>
</form>

<?php if (isset($error)): ?>
    <p><?= $error ?></p>
<?php elseif (isset($success)): ?>
    <p><?= $success ?></p>
<?php endif; ?>
