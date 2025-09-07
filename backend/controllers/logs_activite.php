<?php
require_once(__DIR__ . '/../../backend/config/database.php');

$dateDebut_tmp = $_GET['date_debut'] ?? null;
$dateFin_tmp   = $_GET['date_fin'] ?? null;

$dateDebut = $dateDebut_tmp ? date('Y-m-d', strtotime($dateDebut_tmp)) : null;
$dateFin   = $dateFin_tmp   ? date('Y-m-d', strtotime($dateFin_tmp))   : null;


$sql = "SELECT * FROM logs_activite WHERE 1";
$params = [];

if ($dateDebut) {
  $sql .= " AND cree_le >= :date_debut";
  $params['date_debut'] = $dateDebut;
}
if ($dateFin) {
  $sql .= " AND cree_le <= :date_fin";
  $params['date_fin'] = $dateFin;
}

$sql .= " ORDER BY cree_le DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<table class="table table-bordered table-striped">
  <thead class="table-light">
    <tr>
      <th>ID</th>
      <th>Date Creation</th>
      <th>Admin ID</th>
      <th>Action</th>
      <th>Adresse IP</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= htmlspecialchars($log['logs_activite_id']) ?></td>
        <td><?= date('d/m/Y H:i:s', strtotime($log['cree_le'])) ?></td>
        <td><?= htmlspecialchars($log['admin_id']) ?></td>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= htmlspecialchars($log['ip_address']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>