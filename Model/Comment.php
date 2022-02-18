<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\CommentInterface;

class Comment extends \Magento\Framework\Model\AbstractModel implements CommentInterface
{
    protected function _construct()
    {
        $this->_init(\Vuefront\Blog\Model\ResourceModel\Comment::class);
    }

    public function getId()
    {
        return $this->getData(CommentInterface::COMMENT_ID);
    }
    public function getAuthor()
    {
        return $this->getData(CommentInterface::AUTHOR);
    }
    public function getDescription()
    {
        return $this->getData(CommentInterface::DESCRIPTION);
    }

    public function getPostId()
    {
        return $this->getData(CommentInterface::POST_ID);
    }

    public function getRating()
    {
        return $this->getData(CommentInterface::RATING);
    }

    public function getStatus()
    {
        return $this->getData(CommentInterface::STATUS);
    }

    public function getDateAdded()
    {
        return $this->getData(CommentInterface::DATE_ADDED);
    }

    public function setId($id)
    {
        $this->setData(CommentInterface::COMMENT_ID, $id);
        return $this;
    }
    public function setAuthor($author)
    {
        $this->setData(CommentInterface::AUTHOR, $author);
        return $this;
    }
    public function setDescription($description)
    {
        $this->setData(CommentInterface::DESCRIPTION, $description);
        return $this;
    }

    public function setPostId($post_id)
    {
        $this->setData(CommentInterface::POST_ID, $post_id);
        return $this;
    }

    public function setRating($rating)
    {
        $this->setData(CommentInterface::RATING, $rating);
        return $this;
    }

    public function setStatus($status)
    {
        $this->setData(CommentInterface::STATUS, $status);
        return $this;
    }

    public function setDateAdded($date_added)
    {
        $this->setData(CommentInterface::DATE_ADDED, $date_added);
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['vuefront_blog_comment' . '_' . $this->getId()];
    }
}
