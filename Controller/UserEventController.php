<?php
// Controller/UserEventController.php — v2 (tri, filtre catégorie)

class UserEventController
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = BASE_PATH . '/public/uploads/';
        if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0755, true);
    }

    private function getPdo(): PDO { return Database::getInstance()->getPdo(); }

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'list';
        switch ($action) {
            case 'list':   $this->listEvents(); break;
            case 'search': $this->search();     break;
            case 'filter': $this->filter();     break;
            case 'show':   $this->show();       break;
            case 'create': $this->create();     break;
            case 'store':  $this->store();      break;
            case 'edit':   $this->edit();       break;
            case 'update': $this->update();     break;
            case 'delete':        $this->delete();       break;
            case 'calendar':      $this->calendar();     break;
            case 'calendar_json': $this->calendarJson(); break;
            default:              $this->listEvents();
        }
    }

    private function baseSelect(): string
    {
        return "SELECT e.*, CONCAT(u.first_name,' ',u.last_name) AS organizer_name, c.name AS category_name
                FROM events e
                JOIN utilisateurs u ON e.organizer_id=u.id
                LEFT JOIN event_categories c ON e.event_category_id=c.id";
    }

    private function safeSortClause(): string
    {
        $map = ['title'=>'e.title','event_date'=>'e.event_date','max_participants'=>'e.max_participants','status'=>'e.status'];
        $col   = $_GET['sort']  ?? 'event_date';
        $order = strtoupper($_GET['order'] ?? 'DESC');
        if (!in_array($order,['ASC','DESC'])) $order='DESC';
        $colSql = $map[$col] ?? 'e.event_date';
        return " ORDER BY $colSql $order";
    }

    private function categoryWhere(): string
    {
        $cat = (int)($_GET['category']??0);
        return $cat > 0 ? " AND e.event_category_id=$cat" : '';
    }

    private function loadCategories(): array
    {
        return $this->getPdo()->query("SELECT id, name FROM event_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchStats(): array
    {
        $rows = $this->getPdo()->query("SELECT status, COUNT(*) AS total FROM events GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $map = ['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
        foreach ($rows as $r) { if (isset($map[$r['status']])) $map[$r['status']]=(int)$r['total']; }
        return $map;
    }

    private function listEvents(): void
    {
        $sql    = $this->baseSelect()." WHERE 1=1".$this->categoryWhere().$this->safeSortClause();
        $events = $this->getPdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/listEvents.php';
    }

    private function search(): void
    {
        $q    = trim($_GET['q']??'');
        $like = '%'.$q.'%';
        $stmt = $this->getPdo()->prepare(
            $this->baseSelect()." WHERE (e.title LIKE :q1 OR e.location LIKE :q2 OR e.description LIKE :q3)"
            .$this->categoryWhere().$this->safeSortClause()
        );
        $stmt->execute([':q1'=>$like,':q2'=>$like,':q3'=>$like]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/listEvents.php';
    }

    private function filter(): void
    {
        $status = $_GET['status']??'';
        if ($status !== '') {
            $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE e.status=:status".$this->categoryWhere().$this->safeSortClause());
            $stmt->execute([':status'=>$status]);
        } else {
            $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE 1=1".$this->categoryWhere().$this->safeSortClause());
            $stmt->execute();
        }
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/listEvents.php';
    }

    private function show(): void
    {
        $id   = $this->requireId();
        $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE e.id=:id");
        $stmt->execute([':id'=>$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) $this->redirect('list',null,'not_found');
        require BASE_PATH . '/View/public/eventDetail.php';
    }

    private function create(): void
    {
        $event = null;
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/eventForm.php';
    }

    private function store(): void
    {
        $this->requirePost();
        $data      = $this->sanitize($_POST);
        $imagePath = $this->handleImageUpload();
        $lat  = isset($_POST['latitude'])  && $_POST['latitude']  !== '' ? (float)$_POST['latitude']  : null;
        $lng  = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;
        $stmt = $this->getPdo()->prepare(
            "INSERT INTO events (title,description,event_date,location,is_online,max_participants,status,organizer_id,event_category_id,image_url,latitude,longitude)
             VALUES (:title,:description,:event_date,:location,:is_online,:max_participants,:status,:organizer_id,:event_category_id,:image_url,:latitude,:longitude)"
        );
        $ok = $stmt->execute([
            ':title'=>$data['title'],':description'=>$data['description'],':event_date'=>$data['event_date'],
            ':location'=>$data['location'],':is_online'=>isset($_POST['is_online'])?1:0,
            ':max_participants'=>(int)($data['max_participants']??50),':status'=>$data['status'],
            ':organizer_id'=>1,':event_category_id'=>(int)$data['category_id'],':image_url'=>$imagePath??'',
            ':latitude'=>$lat,':longitude'=>$lng,
        ]);
        $ok ? $this->redirect('list','created') : $this->redirect('create',null,'save_failed');
    }

    private function edit(): void
    {
        $id   = $this->requireId();
        $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE e.id=:id");
        $stmt->execute([':id'=>$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) $this->redirect('list',null,'not_found');
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/eventForm.php';
    }

    private function update(): void
    {
        $this->requirePost();
        $id        = (int)($_POST['id']??0);
        $data      = $this->sanitize($_POST);
        $imagePath = $this->handleImageUpload();
        $imageUrl  = $imagePath ?? trim($_POST['existing_image_url']??'');
        $lat  = isset($_POST['latitude'])  && $_POST['latitude']  !== '' ? (float)$_POST['latitude']  : null;
        $lng  = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? (float)$_POST['longitude'] : null;
        $stmt = $this->getPdo()->prepare(
            "UPDATE events SET title=:title,description=:description,event_date=:event_date,location=:location,
             is_online=:is_online,max_participants=:max_participants,status=:status,
             event_category_id=:event_category_id,image_url=:image_url,
             latitude=:latitude,longitude=:longitude WHERE id=:id"
        );
        $ok = $stmt->execute([
            ':title'=>$data['title'],':description'=>$data['description'],':event_date'=>$data['event_date'],
            ':location'=>$data['location'],':is_online'=>isset($_POST['is_online'])?1:0,
            ':max_participants'=>(int)($data['max_participants']??50),':status'=>$data['status'],
            ':event_category_id'=>(int)$data['category_id'],':image_url'=>$imageUrl,
            ':latitude'=>$lat,':longitude'=>$lng,':id'=>$id,
        ]);
        $ok ? $this->redirect('list','updated') : $this->redirect('edit',null,'save_failed',$id);
    }

    private function calendar(): void
    {
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/public/calendar.php';
    }

    private function calendarJson(): void
    {
        // Palette de couleurs par catégorie (déterministe sur le nom)
        $palette = [
            'intelligence artificielle' => '#6c63ff',
            'machine learning'          => '#6c63ff',
            'ia '                       => '#6c63ff',
            'data science'              => '#3b82f6',
            'big data'                  => '#3b82f6',
            'data'                      => '#3b82f6',
            'cyber'                     => '#ef4444',
            'sécurité'                  => '#ef4444',
            'devops'                    => '#f97316',
            'cloud'                     => '#f97316',
            'blockchain'                => '#f59e0b',
            'web3'                      => '#f59e0b',
            'ux'                        => '#22c55e',
            'ui'                        => '#22c55e',
            'design'                    => '#22c55e',
            'mobile'                    => '#14b8a6',
            'flutter'                   => '#14b8a6',
            'réalité'                   => '#ec4899',
            'vr'                        => '#ec4899',
            'ar '                       => '#ec4899',
            'métavers'                  => '#ec4899',
            'robotique'                 => '#06b6d4',
            'iot'                       => '#06b6d4',
            'embarqué'                  => '#06b6d4',
            'green'                     => '#10b981',
            'tech for good'             => '#10b981',
            'product'                   => '#6366f1',
            'agilité'                   => '#6366f1',
            'growth'                    => '#d97706',
            'marketing'                 => '#d97706',
            'it'                        => '#3b82f6',
            'informatique'              => '#3b82f6',
        ];

        $fallbacks = ['#6c63ff','#3b82f6','#22c55e','#f97316','#ec4899',
                      '#14b8a6','#f59e0b','#06b6d4','#ef4444','#10b981','#6366f1','#d97706'];

        $catColorCache = [];

        $getColor = function(string $catName, int $catId) use ($palette, $fallbacks, &$catColorCache): string {
            if (isset($catColorCache[$catId])) return $catColorCache[$catId];
            $lower = mb_strtolower($catName);
            foreach ($palette as $keyword => $color) {
                if (str_contains($lower, trim($keyword))) {
                    return $catColorCache[$catId] = $color;
                }
            }
            return $catColorCache[$catId] = $fallbacks[$catId % count($fallbacks)];
        };

        $stmt = $this->getPdo()->query(
            "SELECT e.id, e.title, e.event_date, e.location, e.is_online,
                    e.status, e.event_category_id,
                    c.name AS category_name
             FROM events e
             LEFT JOIN event_categories c ON e.event_category_id = c.id
             WHERE e.status IN ('upcoming','ongoing')
             ORDER BY e.event_date ASC"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($rows as $r) {
            $color = $getColor($r['category_name'] ?? '', (int)$r['event_category_id']);
            $events[] = [
                'id'              => $r['id'],
                'title'           => $r['title'],
                'start'           => date('Y-m-d\TH:i:s', strtotime($r['event_date'])),
                'url'             => 'index.php?action=show&id=' . $r['id'],
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'category'  => $r['category_name'] ?? 'Autre',
                    'location'  => $r['is_online'] ? '🌐 En ligne' : '📍 ' . $r['location'],
                    'is_online' => (bool)$r['is_online'],
                    'status'    => $r['status'],
                    'color'     => $color,
                ],
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($events, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function delete(): void
    {
        $id = $this->requireId();
        $this->getPdo()->prepare("DELETE FROM events WHERE id=:id")->execute([':id'=>$id]);
        $this->redirect('list','deleted');
    }

    private function sanitize(array $data): array
    {
        $clean=[];
        foreach($data as $k=>$v){ $clean[$k]=trim(htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8')); }
        return $clean;
    }

    private function handleImageUpload(): ?string
    {
        if (empty($_FILES['image']['tmp_name'])) return null;
        $file    = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($file['type'],$allowed)||$file['size']>5*1024*1024) return null;
        $ext      = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        $filename = uniqid('event_',true).'.'.$ext;
        if (!move_uploaded_file($file['tmp_name'],$this->uploadDir.$filename)) return null;
        return 'uploads/'.$filename;
    }

    private function requirePost(): void { if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('list'); }
    private function requireId(): int { $id=(int)($_GET['id']??$_POST['id']??0); if($id<=0) $this->redirect('list',null,'invalid_id'); return $id; }
    private function redirect(string $action, ?string $success=null, ?string $error=null, ?int $id=null): void
    {
        $url="index.php?action=$action";
        if($id) $url.="&id=$id"; if($success) $url.="&success=$success"; if($error) $url.="&error=$error";
        header("Location: $url"); exit;
    }
}
