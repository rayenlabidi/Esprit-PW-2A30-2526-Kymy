<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workify Admin — Tableau de bord</title>
<?php require BASE_PATH . '/View/shared/_styles_admin.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body>
<div class="layout">

  <?php $activeModule = 'dashboard'; require BASE_PATH . '/View/shared/_sidebar.php'; ?>

  <main class="main">

    <!-- ── Topbar ── -->
    <div class="topbar">
      <h1 class="page-title">Tableau de <span>Bord</span></h1>
      <div class="topbar-actions">
        <span style="font-size:.82rem;color:var(--muted);">Dernière mise à jour : <?= date('d/m/Y H:i') ?></span>
        <a href="index.php?module=events&action=list" class="btn btn-primary">📅 Gérer les événements</a>
      </div>
    </div>

    <div class="page-content">

      <!-- ── KPI Cards ── -->
      <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">

        <div class="stat-card total">
          <div class="stat-icon">📅</div>
          <span class="stat-label">Total événements</span>
          <span class="stat-value"><?= $stats['total'] ?></span>
          <span class="stat-sub"><?= $stats['this_month'] ?> ce mois-ci</span>
        </div>

        <div class="stat-card upcoming">
          <div class="stat-icon">🔜</div>
          <span class="stat-label">À venir</span>
          <span class="stat-value"><?= $stats['upcoming'] ?></span>
        </div>

        <div class="stat-card ongoing">
          <div class="stat-icon">▶️</div>
          <span class="stat-label">En cours</span>
          <span class="stat-value"><?= $stats['ongoing'] ?></span>
        </div>

        <div class="stat-card completed">
          <div class="stat-icon">✅</div>
          <span class="stat-label">Terminés</span>
          <span class="stat-value"><?= $stats['completed'] ?></span>
        </div>

        <div class="stat-card cancelled">
          <div class="stat-icon">❌</div>
          <span class="stat-label">Annulés</span>
          <span class="stat-value"><?= $stats['cancelled'] ?></span>
        </div>

        <div class="stat-card online">
          <div class="stat-icon">🌐</div>
          <span class="stat-label">En ligne</span>
          <span class="stat-value"><?= $stats['online'] ?></span>
          <span class="stat-sub"><?= $stats['total'] > 0 ? round($stats['online'] / $stats['total'] * 100) : 0 ?>% du total</span>
        </div>

        <div class="stat-card" style="border-left-color:#f59e0b;">
          <div class="stat-icon">👥</div>
          <span class="stat-label">Participants (max)</span>
          <span class="stat-value" style="color:#f59e0b;font-size:1.6rem;"><?= number_format($stats['total_participants']) ?></span>
          <span class="stat-sub">capacité totale</span>
        </div>

        <div class="stat-card" style="border-left-color:#8b5cf6;">
          <div class="stat-icon">📁</div>
          <span class="stat-label">Catégories actives</span>
          <span class="stat-value" style="color:#8b5cf6;"><?= $stats['categories'] ?></span>
        </div>

      </div>

      <!-- ── Charts Grid ── -->
      <div class="charts-grid" style="grid-template-columns:1fr 1fr;">

        <!-- Événements par statut -->
        <div class="chart-card">
          <div class="chart-title">🎯 Répartition par statut</div>
          <canvas id="chartStatus" height="220"></canvas>
        </div>

        <!-- Événements par catégorie -->
        <div class="chart-card">
          <div class="chart-title">📁 Événements par catégorie</div>
          <canvas id="chartCategory" height="220"></canvas>
        </div>

        <!-- Timeline mensuelle -->
        <div class="chart-card" style="grid-column:1/-1;">
          <div class="chart-title">📈 Création d'événements par mois (<?= date('Y') ?>)</div>
          <canvas id="chartMonthly" height="120"></canvas>
        </div>

        <!-- Online vs Présentiel -->
        <div class="chart-card">
          <div class="chart-title">🌐 En ligne vs Présentiel</div>
          <canvas id="chartOnline" height="180"></canvas>
        </div>

        <!-- Participants par catégorie -->
        <div class="chart-card">
          <div class="chart-title">👥 Capacité par catégorie</div>
          <canvas id="chartParticipants" height="180"></canvas>
        </div>

      </div>

      <!-- ── Derniers événements ── -->
      <div class="card">
        <div class="card-header">
          <span>🕐 5 derniers événements créés</span>
          <a href="index.php?module=events&action=list" class="btn btn-sm btn-secondary">Voir tout</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>Événement</th>
              <th>Date</th>
              <th>Catégorie</th>
              <th>Statut</th>
              <th>Participants</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($recentEvents as $e): ?>
            <tr>
              <td>
                <div class="event-title"><?= htmlspecialchars($e['title']) ?></div>
                <div class="event-meta"><?= htmlspecialchars($e['organizer_name']) ?></div>
              </td>
              <td><?= date('d/m/Y', strtotime($e['event_date'])) ?></td>
              <td><?= htmlspecialchars($e['category_name'] ?? '—') ?></td>
              <td><span class="badge badge-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span></td>
              <td><?= $e['max_participants'] ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div><!-- /.page-content -->
  </main>
</div>

<script>
// ── Données injectées depuis PHP ──
const dataStatus = <?= json_encode(array_values($chartData['status'])) ?>;
const labelsStatus = <?= json_encode(array_keys($chartData['status'])) ?>;
const labelsFr = { upcoming:'À venir', ongoing:'En cours', completed:'Terminés', cancelled:'Annulés' };
const labelsStatusFr = labelsStatus.map(k => labelsFr[k] || k);

const dataCategory = <?= json_encode(array_values($chartData['by_category'])) ?>;
const labelsCategory = <?= json_encode(array_keys($chartData['by_category'])) ?>;

const dataMonthly = <?= json_encode(array_values($chartData['monthly'])) ?>;
const labelsMonthly = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

const onlineCount = <?= $stats['online'] ?>;
const onsiteCount = <?= $stats['total'] - $stats['online'] ?>;

const dataParticipants = <?= json_encode(array_values($chartData['participants_by_cat'])) ?>;
const labelsParticipants = <?= json_encode(array_keys($chartData['participants_by_cat'])) ?>;

// ── Palette de couleurs ──
const palette = ['#6c63ff','#ff6584','#22c55e','#f59e0b','#3b82f6','#8b5cf6','#ec4899','#14b8a6'];
const paletteStatus = ['#3b82f6','#22c55e','#9ca3af','#ef4444'];

Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
Chart.defaults.color = '#6b7280';

// ── Pie: Statuts ──
new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut',
  data: {
    labels: labelsStatusFr,
    datasets: [{ data: dataStatus, backgroundColor: paletteStatus, borderWidth: 2, borderColor: '#fff' }]
  },
  options: {
    plugins: { legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } } },
    cutout: '62%'
  }
});

// ── Bar: Par catégorie ──
new Chart(document.getElementById('chartCategory'), {
  type: 'bar',
  data: {
    labels: labelsCategory,
    datasets: [{
      label: 'Événements',
      data: dataCategory,
      backgroundColor: palette,
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0eeff' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Line: Timeline mensuelle ──
new Chart(document.getElementById('chartMonthly'), {
  type: 'line',
  data: {
    labels: labelsMonthly,
    datasets: [{
      label: 'Événements créés',
      data: dataMonthly,
      borderColor: '#6c63ff',
      backgroundColor: 'rgba(108,99,255,.08)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#6c63ff',
      pointRadius: 4
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0eeff' } },
      x: { grid: { display: false } }
    }
  }
});

// ── Doughnut: Online vs Présentiel ──
new Chart(document.getElementById('chartOnline'), {
  type: 'doughnut',
  data: {
    labels: ['🌐 En ligne', '📍 Présentiel'],
    datasets: [{
      data: [onlineCount, onsiteCount],
      backgroundColor: ['#6c63ff', '#f59e0b'],
      borderWidth: 2,
      borderColor: '#fff'
    }]
  },
  options: {
    plugins: { legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } } },
    cutout: '55%'
  }
});

// ── Bar: Capacité par catégorie ──
new Chart(document.getElementById('chartParticipants'), {
  type: 'bar',
  data: {
    labels: labelsParticipants,
    datasets: [{
      label: 'Capacité max',
      data: dataParticipants,
      backgroundColor: palette.map(c => c + 'cc'),
      borderColor: palette,
      borderWidth: 2,
      borderRadius: 6,
      borderSkipped: false
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: { legend: { display: false } },
    scales: {
      x: { beginAtZero: true, grid: { color: '#f0eeff' } },
      y: { grid: { display: false } }
    }
  }
});
</script>

</body>
</html>
