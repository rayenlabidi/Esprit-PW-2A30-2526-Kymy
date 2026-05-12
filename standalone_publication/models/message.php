<?php
class message
{
    private $id;
    private $sender_id;
    private $receiver_id;
    private $sender_name;
    private $receiver_name;
    private $sender_init;
    private $receiver_init;
    private $sender_avatar;
    private $receiver_avatar;
    private $publication_id;  // NEW: link to publication
    private $content;
    private $is_read;
    private $is_flagged;
    private $created_at;
    private $updated_at;

    public function __construct($sender_id, $receiver_id, $sender_name, $receiver_name, $sender_init, $receiver_init, $content, $sender_avatar = 'av-blue', $receiver_avatar = 'av-blue', $publication_id = null)
    {
        $this->sender_id = $sender_id;
        $this->receiver_id = $receiver_id;
        $this->sender_name = $sender_name;
        $this->receiver_name = $receiver_name;
        $this->sender_init = $sender_init;
        $this->receiver_init = $receiver_init;
        $this->content = $content;
        $this->sender_avatar = $sender_avatar;
        $this->receiver_avatar = $receiver_avatar;
        $this->publication_id = $publication_id;
        $this->is_read = 0;
        $this->is_flagged = 0;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getSenderId() { return $this->sender_id; }
    public function getReceiverId() { return $this->receiver_id; }
    public function getSenderName() { return $this->sender_name; }
    public function getReceiverName() { return $this->receiver_name; }
    public function getSenderInit() { return $this->sender_init; }
    public function getReceiverInit() { return $this->receiver_init; }
    public function getSenderAvatar() { return $this->sender_avatar; }
    public function getReceiverAvatar() { return $this->receiver_avatar; }
    public function getPublicationId() { return $this->publication_id; }  // NEW
    public function getContent() { return $this->content; }
    public function getIsRead() { return $this->is_read; }
    public function getIsFlagged() { return $this->is_flagged; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setSenderId($sender_id) { $this->sender_id = $sender_id; }
    public function setReceiverId($receiver_id) { $this->receiver_id = $receiver_id; }
    public function setSenderName($sender_name) { $this->sender_name = $sender_name; }
    public function setReceiverName($receiver_name) { $this->receiver_name = $receiver_name; }
    public function setSenderInit($sender_init) { $this->sender_init = $sender_init; }
    public function setReceiverInit($receiver_init) { $this->receiver_init = $receiver_init; }
    public function setSenderAvatar($sender_avatar) { $this->sender_avatar = $sender_avatar; }
    public function setReceiverAvatar($receiver_avatar) { $this->receiver_avatar = $receiver_avatar; }
    public function setPublicationId($publication_id) { $this->publication_id = $publication_id; }  // NEW
    public function setContent($content) { $this->content = $content; }
    public function setIsRead($is_read) { $this->is_read = $is_read; }
    public function setIsFlagged($is_flagged) { $this->is_flagged = $is_flagged; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }
}
?>