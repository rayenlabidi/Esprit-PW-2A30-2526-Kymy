<?php
class publication
{
    private $userId;
    private $userName;
    private $userInit;
    private $userRole;
    private $userAvatar;
    private $content;
    private $imageUrl;

    public function __construct($userId, $userName, $userInit, $userRole, $userAvatar, $content, $imageUrl = '')
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userInit = $userInit;
        $this->userRole = $userRole;
        $this->userAvatar = $userAvatar;
        $this->content = $content;
        $this->imageUrl = $imageUrl;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function getUserInit()
    {
        return $this->userInit;
    }

    public function setUserInit($userInit)
    {
        $this->userInit = $userInit;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    public function setUserRole($userRole)
    {
        $this->userRole = $userRole;
    }

    public function getUserAvatar()
    {
        return $this->userAvatar;
    }

    public function setUserAvatar($userAvatar)
    {
        $this->userAvatar = $userAvatar;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }
}
?>
