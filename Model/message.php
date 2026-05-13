<?php
class message
{
    private $senderId;
    private $receiverId;
    private $senderName;
    private $receiverName;
    private $senderInit;
    private $receiverInit;
    private $senderAvatar;
    private $receiverAvatar;
    private $publicationId;
    private $content;

    public function __construct($senderId, $receiverId, $senderName, $receiverName, $senderInit, $receiverInit, $senderAvatar, $receiverAvatar, $content, $publicationId = null)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->senderName = $senderName;
        $this->receiverName = $receiverName;
        $this->senderInit = $senderInit;
        $this->receiverInit = $receiverInit;
        $this->senderAvatar = $senderAvatar;
        $this->receiverAvatar = $receiverAvatar;
        $this->content = $content;
        $this->publicationId = $publicationId;
    }

    public function getSenderId()
    {
        return $this->senderId;
    }

    public function getReceiverId()
    {
        return $this->receiverId;
    }

    public function getSenderName()
    {
        return $this->senderName;
    }

    public function getReceiverName()
    {
        return $this->receiverName;
    }

    public function getSenderInit()
    {
        return $this->senderInit;
    }

    public function getReceiverInit()
    {
        return $this->receiverInit;
    }

    public function getSenderAvatar()
    {
        return $this->senderAvatar;
    }

    public function getReceiverAvatar()
    {
        return $this->receiverAvatar;
    }

    public function getPublicationId()
    {
        return $this->publicationId;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
}
?>
