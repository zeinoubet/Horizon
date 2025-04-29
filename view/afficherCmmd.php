<table>
    <tr>
        <th>ID</th>
        <th>Nom du Client</th>
        <th>Quantité</th>
        <th>Adresse</th>
        <th>Total</th>
    </tr>
    <?php foreach ($commandes as $commande) { ?>
        <tr>
            <td><?= $commande['id'] ?></td>
            <td><?= $commande['client_nom'] ?></td>
            <td><?= $commande['quantite'] ?></td>
            <td><?= $commande['adresse'] ?></td>
            <td><?= $commande['total'] ?> €</td>
        </tr>
    <?php } ?>
</table>
