<?php
class comment
{
    private $id;
    private $publication_id;
    private $user_name;
    private $user_init;
    private $user_avatar;
    private $comment;
    private $likes;
    private $parent_id;
    private $created_at;

    public function __construct($publication_id, $user_name, $user_init, $user_avatar, $comment, $parent_id = null)
    {
        $this->publication_id = $publication_id;
        $this->user_name = $user_name;
        $this->user_init = $user_init;
        $this->user_avatar = $user_avatar;
        $this->comment = $comment;
        $this->parent_id = $parent_id;
        $this->likes = 0;
    }

    public function getId() { return $this->id; }
    public function getPublicationId() { return $this->publication_id; }
    public function getUserName() { return $this->user_name; }
    public function getUserInit() { return $this->user_init; }
    public function getUserAvatar() { return $this->user_avatar; }
    public function getComment() { return $this->comment; }
    public function getLikes() { return $this->likes; }
    public function getParentId() { return $this->parent_id; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setPublicationId($publication_id) { $this->publication_id = $publication_id; }
    public function setUserName($user_name) { $this->user_name = $user_name; }
    public function setUserInit($user_init) { $this->user_init = $user_init; }
    public function setUserAvatar($user_avatar) { $this->user_avatar = $user_avatar; }
    public function setComment($comment) { $this->comment = $comment; }
    public function setLikes($likes) { $this->likes = $likes; }
    public function setParentId($parent_id) { $this->parent_id = $parent_id; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
}
?>