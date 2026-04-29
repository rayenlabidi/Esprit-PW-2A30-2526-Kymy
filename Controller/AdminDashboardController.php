<?php
// Controller/AdminDashboardController.php — Tableau de bord statistiques

class AdminDashboardController
{
    private function getPdo(): PDO { return Database::getInstance()->getPdo(); }

    public function handleRequest(): void
    {
        $this->dashboard();
    }

    private function dashboard(): void
    {
        $pdo = $this->getPdo();

        // ── KPI globaux ──
        $year = (int)date('Y');

        $byStatus = $pdo->query("SELECT status, COUNT(*) c FROM events GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $statusMap = ['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
        foreach ($byStatus as $r) { if (isset($statusMap[$r['status']])) $statusMap[$r['status']] = (int)$r['c']; }

        $totalEvents   = array_sum($statusMap);
        $onlineCount   = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE is_online=1")->fetchColumn();
        $totalPart     = (int)$pdo->query("SELECT COALESCE(SUM(max_participants),0) FROM events")->fetchColumn();
        $catCount      = (int)$pdo->query("SELECT COUNT(*) FROM event_categories")->fetchColumn();
        $thisMonth     = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();

        $stats = array_merge($statusMap, [
            'total'              => $totalEvents,
            'online'             => $onlineCount,
            'total_participants' => $totalPart,
            'categories'         => $catCount,
            'this_month'         => $thisMonth,
        ]);

        // ── Chart: par catégorie ──
        $catRows = $pdo->query(
            "SELECT c.name, COUNT(e.id) AS cnt
             FROM event_categories c
             LEFT JOIN events e ON e.event_category_id = c.id
             GROUP BY c.id, c.name ORDER BY cnt DESC LIMIT 8"
        )->fetchAll(PDO::FETCH_ASSOC);
        $byCategory = [];
        foreach ($catRows as $r) $byCategory[$r['name']] = (int)$r['cnt'];

        // ── Chart: mensuel ──
        $monthly = array_fill(1, 12, 0);
        try {
            $mRows = $pdo->query(
                "SELECT MONTH(event_date) AS m, COUNT(*) AS cnt FROM events WHERE YEAR(event_date)=$year GROUP BY m"
            )->fetchAll(PDO::FETCH_ASSOC);
            foreach ($mRows as $r) $monthly[(int)$r['m']] = (int)$r['cnt'];
        } catch (\Exception $e) {}

        // ── Chart: participants par catégorie ──
        $partRows = $pdo->query(
            "SELECT c.name, COALESCE(SUM(e.max_participants),0) AS total
             FROM event_categories c
             LEFT JOIN events e ON e.event_category_id = c.id
             GROUP BY c.id, c.name ORDER BY total DESC LIMIT 6"
        )->fetchAll(PDO::FETCH_ASSOC);
        $partByCat = [];
        foreach ($partRows as $r) $partByCat[$r['name']] = (int)$r['total'];

        $chartData = [
            'status'             => $statusMap,
            'by_category'        => $byCategory,
            'monthly'            => array_values($monthly),
            'participants_by_cat'=> $partByCat,
        ];

        // ── 5 derniers événements ──
        $recentEvents = $pdo->query(
            "SELECT e.*, CONCAT(u.first_name,' ',u.last_name) AS organizer_name, c.name AS category_name
             FROM events e
             JOIN utilisateurs u ON e.organizer_id=u.id
             LEFT JOIN event_categories c ON e.event_category_id=c.id
             ORDER BY e.id DESC LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        require BASE_PATH . '/View/admin/dashboard.php';
    }
}
