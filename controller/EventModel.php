<?php
require_once __DIR__ . '/../config.php';

class EventModel
{
    public function listeEvents($search = '', $category = '', $status = '', $sort = 'date_desc')
    {
        $sql = 'SELECT e.*, c.name AS category_name, CONCAT(u.first_name, " ", u.last_name) AS organizer_name
                FROM events e
                INNER JOIN utilisateurs u ON e.organizer_id = u.id
                LEFT JOIN event_categories c ON e.event_category_id = c.id
                WHERE 1 = 1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (e.title LIKE :search OR e.description LIKE :search OR e.location LIKE :search OR c.name LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($category !== '') {
            $sql .= ' AND e.event_category_id = :category';
            $params['category'] = (int) $category;
        }

        if ($status !== '') {
            $sql .= ' AND e.status = :status';
            $params['status'] = $status;
        }

        if ($sort === 'title') {
            $sql .= ' ORDER BY e.title ASC';
        } elseif ($sort === 'capacity') {
            $sql .= ' ORDER BY e.max_participants DESC';
        } elseif ($sort === 'date_asc') {
            $sql .= ' ORDER BY e.event_date ASC';
        } else {
            $sql .= ' ORDER BY e.event_date DESC, e.id DESC';
        }

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute($params);
        return $query->fetchAll();
    }

    public function getEventById($id)
    {
        $sql = 'SELECT e.*, c.name AS category_name, CONCAT(u.first_name, " ", u.last_name) AS organizer_name
                FROM events e
                INNER JOIN utilisateurs u ON e.organizer_id = u.id
                LEFT JOIN event_categories c ON e.event_category_id = c.id
                WHERE e.id = :id';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => (int) $id]);
        return $query->fetch();
    }

    public function listeCategories()
    {
        $db = config::getConnexion();
        return $db->query('SELECT * FROM event_categories ORDER BY name ASC')->fetchAll();
    }

    public function getCategoryById($id)
    {
        $db = config::getConnexion();
        $query = $db->prepare('SELECT * FROM event_categories WHERE id = :id');
        $query->execute(['id' => (int) $id]);
        return $query->fetch();
    }

    public function addCategory($data)
    {
        $db = config::getConnexion();
        $query = $db->prepare('INSERT INTO event_categories (name, description) VALUES (:name, :description)');
        $query->execute([
            'name' => trim($data['name']),
            'description' => trim($data['description'])
        ]);
    }

    public function updateCategory($id, $data)
    {
        $db = config::getConnexion();
        $query = $db->prepare('UPDATE event_categories SET name = :name, description = :description WHERE id = :id');
        $query->execute([
            'name' => trim($data['name']),
            'description' => trim($data['description']),
            'id' => (int) $id
        ]);
    }

    public function deleteCategory($id)
    {
        $db = config::getConnexion();
        $query = $db->prepare('DELETE FROM event_categories WHERE id = :id');
        $query->execute(['id' => (int) $id]);
    }

    public function calendarEvents()
    {
        $sql = 'SELECT e.id, e.title, e.event_date, e.location, e.is_online, e.status, e.event_category_id, c.name AS category_name
                FROM events e
                LEFT JOIN event_categories c ON e.event_category_id = c.id
                WHERE e.status IN ("upcoming", "ongoing")
                ORDER BY e.event_date ASC';
        $db = config::getConnexion();
        return $db->query($sql)->fetchAll();
    }

    public function addEvent($data)
    {
        $sql = 'INSERT INTO events
                (title, description, event_date, location, is_online, max_participants, status, organizer_id, event_category_id, image_url, latitude, longitude)
                VALUES
                (:title, :description, :event_date, :location, :is_online, :max_participants, :status, :organizer_id, :event_category_id, :image_url, :latitude, :longitude)';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute($this->payload($data));
    }

    public function updateEvent($id, $data)
    {
        $sql = 'UPDATE events SET
                    title = :title,
                    description = :description,
                    event_date = :event_date,
                    location = :location,
                    is_online = :is_online,
                    max_participants = :max_participants,
                    status = :status,
                    organizer_id = :organizer_id,
                    event_category_id = :event_category_id,
                    image_url = :image_url,
                    latitude = :latitude,
                    longitude = :longitude
                WHERE id = :id';
        $params = $this->payload($data);
        $params['id'] = (int) $id;
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute($params);
    }

    public function deleteEvent($id)
    {
        $db = config::getConnexion();
        $query = $db->prepare('DELETE FROM events WHERE id = :id');
        $query->execute(['id' => (int) $id]);
    }

    public function statistiquesEvents()
    {
        $db = config::getConnexion();
        $row = $db->query("SELECT
                    COUNT(*) AS total,
                    IFNULL(SUM(CASE WHEN status = 'upcoming' THEN 1 ELSE 0 END), 0) AS upcoming,
                    IFNULL(SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END), 0) AS ongoing,
                    IFNULL(SUM(CASE WHEN is_online = 1 THEN 1 ELSE 0 END), 0) AS online
                FROM events")->fetch();

        return $row ?: ['total' => 0, 'upcoming' => 0, 'ongoing' => 0, 'online' => 0];
    }

    public function registerForEvent($eventId, $userId)
    {
        $this->ensureRegistrationsTable();
        $db = config::getConnexion();
        $query = $db->prepare('INSERT IGNORE INTO event_registrations (event_id, user_id) VALUES (:event_id, :user_id)');
        $query->execute([
            'event_id' => (int) $eventId,
            'user_id' => (int) $userId
        ]);
    }
    public function isRegistered($eventId, $userId)
    {
        if ((int) $userId <= 0) {
            return false;
        }
        $this->ensureRegistrationsTable();
        $db = config::getConnexion();
        $query = $db->prepare('SELECT COUNT(*) FROM event_registrations WHERE event_id = :event_id AND user_id = :user_id');
        $query->execute([
            'event_id' => (int) $eventId,
            'user_id' => (int) $userId
        ]);
        return (int) $query->fetchColumn() > 0;
    }
    private function ensureRegistrationsTable()
    {
        $db = config::getConnexion();
        $db->exec("CREATE TABLE IF NOT EXISTS event_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_event_user (event_id, user_id),
            CONSTRAINT fk_event_registrations_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_event_registrations_user FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }    private function payload($data)
    {
        return [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'event_date' => str_replace('T', ' ', trim($data['event_date'])),
            'location' => trim($data['location']),
            'is_online' => !empty($data['is_online']) ? 1 : 0,
            'max_participants' => (int) $data['max_participants'],
            'status' => trim($data['status']),
            'organizer_id' => (int) $data['organizer_id'],
            'event_category_id' => (int) $data['category_id'],
            'image_url' => trim($data['image_url']),
            'latitude' => trim($data['latitude']) !== '' ? (float) $data['latitude'] : null,
            'longitude' => trim($data['longitude']) !== '' ? (float) $data['longitude'] : null
        ];
    }
}
?>
