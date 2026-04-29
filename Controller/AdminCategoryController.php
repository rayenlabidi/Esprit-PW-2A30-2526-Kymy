<?php
// Controller/AdminCategoryController.php — v2 (tri)

class AdminCategoryController
{
    private function getPdo(): PDO { return Database::getInstance()->getPdo(); }

    public function handleRequest(): void
    {
        $action = $_GET['action'] ?? 'list';
        switch ($action) {
            case 'list':   $this->listCats(); break;
            case 'create': $this->create();   break;
            case 'store':  $this->store();    break;
            case 'edit':   $this->edit();     break;
            case 'update': $this->update();   break;
            case 'delete': $this->delete();   break;
            default:       $this->listCats();
        }
    }

    private function safeSortClause(): string
    {
        $map = ['name'=>'c.name','event_count'=>'event_count'];
        $col   = $_GET['sort']  ?? 'name';
        $order = strtoupper($_GET['order'] ?? 'ASC');
        if (!in_array($order,['ASC','DESC'])) $order='ASC';
        $colSql = $map[$col] ?? 'c.name';
        return " ORDER BY $colSql $order";
    }

    private function listCats(): void
    {
        $sql = "SELECT c.*, COUNT(e.id) AS event_count
                FROM event_categories c
                LEFT JOIN events e ON e.event_category_id = c.id
                GROUP BY c.id".$this->safeSortClause();
        $categories = $this->getPdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        require BASE_PATH . '/View/admin/listCategories.php';
    }

    private function create(): void
    {
        $category = null;
        require BASE_PATH . '/View/admin/categoryForm.php';
    }

    private function store(): void
    {
        $this->requirePost();
        $data = $this->sanitize($_POST);
        $stmt = $this->getPdo()->prepare("INSERT INTO event_categories (name,description) VALUES (:name,:description)");
        $ok   = $stmt->execute([':name'=>$data['name'],':description'=>$data['description']??'']);
        $ok ? $this->redirect('list','created') : $this->redirect('create',null,'save_failed');
    }

    private function edit(): void
    {
        $id   = $this->requireId();
        $stmt = $this->getPdo()->prepare("SELECT * FROM event_categories WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$category) $this->redirect('list',null,'not_found');
        require BASE_PATH . '/View/admin/categoryForm.php';
    }

    private function update(): void
    {
        $this->requirePost();
        $id   = (int)($_POST['id']??0);
        $data = $this->sanitize($_POST);
        $stmt = $this->getPdo()->prepare("UPDATE event_categories SET name=:name,description=:description WHERE id=:id");
        $ok   = $stmt->execute([':name'=>$data['name'],':description'=>$data['description']??'',':id'=>$id]);
        $ok ? $this->redirect('list','updated') : $this->redirect('edit',null,'save_failed',$id);
    }

    private function delete(): void
    {
        $id = $this->requireId();
        $chk = $this->getPdo()->prepare("SELECT COUNT(*) AS total FROM events WHERE event_category_id=:id");
        $chk->execute([':id'=>$id]);
        if ((int)$chk->fetch()['total'] > 0) { $this->redirect('list',null,'has_events'); return; }
        $this->getPdo()->prepare("DELETE FROM event_categories WHERE id=:id")->execute([':id'=>$id]);
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
        $url="index.php?module=categories&action=$action";
        if($id) $url.="&id=$id"; if($success) $url.="&success=$success"; if($error) $url.="&error=$error";
        header("Location: $url"); exit;
    }
}
