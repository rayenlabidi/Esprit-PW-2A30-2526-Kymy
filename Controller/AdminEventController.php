<?php
// Controller/AdminEventController.php — v2 (tri, stats, filtre catégorie)

class AdminEventController
{
    private function getPdo(): PDO { return Database::getInstance()->getPdo(); }

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'list';
        switch ($action) {
            case 'list':   $this->listEvents();  break;
            case 'create': $this->create();       break;
            case 'store':  $this->store();        break;
            case 'edit':   $this->edit();         break;
            case 'update': $this->update();       break;
            case 'delete': $this->delete();       break;
            case 'search': $this->search();       break;
            case 'filter': $this->filter();       break;
            default:       $this->listEvents();
        }
    }

    private function baseSelect(): string
    {
        return "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) AS organizer_name, c.name AS category_name
                FROM events e
                JOIN utilisateurs u ON e.organizer_id = u.id
                LEFT JOIN event_categories c ON e.event_category_id = c.id";
    }

    private function safeSortClause(): string
    {
        $map = ['title'=>'e.title','event_date'=>'e.event_date','location'=>'e.location','max_participants'=>'e.max_participants','status'=>'e.status','category_name'=>'c.name'];
        $col   = $_GET['sort']  ?? 'event_date';
        $order = strtoupper($_GET['order'] ?? 'DESC');
        if (!in_array($order, ['ASC','DESC'])) $order = 'DESC';
        $colSql = $map[$col] ?? 'e.event_date';
        return " ORDER BY $colSql $order";
    }

    private function categoryWhere(): string
    {
        $cat = (int)($_GET['category'] ?? 0);
        return $cat > 0 ? " AND e.event_category_id = $cat" : '';
    }

    private function loadCategories(): array
    {
        return $this->getPdo()->query("SELECT id, name FROM event_categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchStats(): array
    {
        $rows = $this->getPdo()->query("SELECT status, COUNT(*) AS total FROM events GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $map = ['upcoming'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
        foreach ($rows as $r) { if (isset($map[$r['status']])) $map[$r['status']] = (int)$r['total']; }
        return $map;
    }

    private function listEvents(): void
    {
        $sql    = $this->baseSelect()." WHERE 1=1".$this->categoryWhere().$this->safeSortClause();
        $events = $this->getPdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/admin/listEvent.php';
    }

    private function search(): void
    {
        $q    = trim($_GET['q'] ?? '');
        $like = '%'.$q.'%';
        $stmt = $this->getPdo()->prepare(
            $this->baseSelect()." WHERE (e.title LIKE :q1 OR e.location LIKE :q2 OR e.description LIKE :q3)"
            .$this->categoryWhere().$this->safeSortClause()
        );
        $stmt->execute([':q1'=>$like,':q2'=>$like,':q3'=>$like]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/admin/listEvent.php';
    }

    private function filter(): void
    {
        $status = $_GET['status'] ?? '';
        if ($status !== '') {
            $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE e.status = :status".$this->categoryWhere().$this->safeSortClause());
            $stmt->execute([':status'=>$status]);
        } else {
            $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE 1=1".$this->categoryWhere().$this->safeSortClause());
            $stmt->execute();
        }
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats  = $this->fetchStats();
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/admin/listEvent.php';
    }

    private function create(): void
    {
        $event = null;
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/admin/eventForm.php';
    }

    private function store(): void
    {
        $this->requirePost();
        $data = $this->sanitize($_POST);
        $stmt = $this->getPdo()->prepare(
            "INSERT INTO events (title,description,event_date,location,is_online,max_participants,status,organizer_id,event_category_id,image_url)
             VALUES (:title,:description,:event_date,:location,:is_online,:max_participants,:status,:organizer_id,:event_category_id,:image_url)"
        );
        $ok = $stmt->execute([
            ':title'=>$data['title'],':description'=>$data['description'],':event_date'=>$data['event_date'],
            ':location'=>$data['location'],':is_online'=>isset($_POST['is_online'])?1:0,
            ':max_participants'=>(int)($data['max_participants']??50),':status'=>$data['status'],
            ':organizer_id'=>max(1,(int)($data['organizer_id']??1)),
            ':event_category_id'=>(int)$data['category_id'],':image_url'=>$data['image_url']??'',
        ]);
        $ok ? $this->redirect('list','created') : $this->redirect('create',null,'save_failed');
    }

    private function edit(): void
    {
        $id   = $this->requireId();
        $stmt = $this->getPdo()->prepare($this->baseSelect()." WHERE e.id = :id");
        $stmt->execute([':id'=>$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$event) $this->redirect('list',null,'not_found');
        $categories = $this->loadCategories();
        require BASE_PATH . '/View/admin/eventForm.php';
    }

    private function update(): void
    {
        $this->requirePost();
        $id   = (int)($_POST['id'] ?? 0);
        $data = $this->sanitize($_POST);
        $stmt = $this->getPdo()->prepare(
            "UPDATE events SET title=:title,description=:description,event_date=:event_date,
             location=:location,is_online=:is_online,max_participants=:max_participants,
             status=:status,event_category_id=:event_category_id,image_url=:image_url WHERE id=:id"
        );
        $ok = $stmt->execute([
            ':title'=>$data['title'],':description'=>$data['description'],':event_date'=>$data['event_date'],
            ':location'=>$data['location'],':is_online'=>isset($_POST['is_online'])?1:0,
            ':max_participants'=>(int)($data['max_participants']??50),':status'=>$data['status'],
            ':event_category_id'=>(int)$data['category_id'],':image_url'=>$data['image_url']??'',':id'=>$id,
        ]);
        $ok ? $this->redirect('list','updated') : $this->redirect('edit',null,'save_failed',$id);
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
    private function requirePost(): void { if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('list'); }
    private function requireId(): int { $id=(int)($_GET['id']??$_POST['id']??0); if($id<=0) $this->redirect('list',null,'invalid_id'); return $id; }
    private function redirect(string $action, ?string $success=null, ?string $error=null, ?int $id=null): void
    {
        $url="index.php?action=$action";
        if($id) $url.="&id=$id"; if($success) $url.="&success=$success"; if($error) $url.="&error=$error";
        header("Location: $url"); exit;
    }
}
