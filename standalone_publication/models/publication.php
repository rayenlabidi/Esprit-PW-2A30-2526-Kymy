<?php
class publication
{
    private $id;
    private $user_id;
    private $user_name;
    private $user_init;
    private $user_role;
    private $user_avatar;
    private $content;
    private $image_url;
    private $likes;
    private $created_at;
    private $updated_at;

    public function __construct($user_name, $user_init, $user_role, $user_avatar, $content, $image_url = '')
    {
        $this->user_name = $user_name;
        $this->user_init = $user_init;
        $this->user_role = $user_role;
        $this->user_avatar = $user_avatar;
        $this->content = $content;
        $this->image_url = $image_url;
        $this->likes = 0;
    }

    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getUserName() { return $this->user_name; }
    public function getUserInit() { return $this->user_init; }
    public function getUserRole() { return $this->user_role; }
    public function getUserAvatar() { return $this->user_avatar; }
    public function getContent() { return $this->content; }
    public function getImageUrl() { return $this->image_url; }
    public function getLikes() { return $this->likes; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setUserName($user_name) { $this->user_name = $user_name; }
    public function setUserInit($user_init) { $this->user_init = $user_init; }
    public function setUserRole($user_role) { $this->user_role = $user_role; }
    public function setUserAvatar($user_avatar) { $this->user_avatar = $user_avatar; }
    public function setContent($content) { $this->content = $content; }
    public function setImageUrl($image_url) { $this->image_url = $image_url; }
    public function setLikes($likes) { $this->likes = $likes; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }
}
?>