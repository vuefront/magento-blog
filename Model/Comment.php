<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\CommentInterface;

class Comment extends \Magento\Framework\Model\AbstractModel implements CommentInterface
{
    /**
     * Comment construct
     */
    protected function _construct()
    {
        $this->_init(\Vuefront\Blog\Model\ResourceModel\Comment::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(CommentInterface::COMMENT_ID);
    }

    /**
     * Get Author
     *
     * @return string|null
     */
    public function getAuthor()
    {
        return $this->getData(CommentInterface::AUTHOR);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(CommentInterface::DESCRIPTION);
    }

    /**
     * Get Parent ID
     *
     * @return int|null
     */
    public function getPostId()
    {
        return $this->getData(CommentInterface::POST_ID);
    }

    /**
     * Get Rating
     *
     * @return int|null
     */
    public function getRating()
    {
        return $this->getData(CommentInterface::RATING);
    }

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData(CommentInterface::STATUS);
    }

    /**
     * Get Date Added
     *
     * @return string|null
     */
    public function getDateAdded()
    {
        return $this->getData(CommentInterface::DATE_ADDED);
    }

    /**
     * Set Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(CommentInterface::COMMENT_ID, $id);
        return $this;
    }

    /**
     * Set Author
     *
     * @param string $author
     * @return $this
     */
    public function setAuthor($author)
    {
        $this->setData(CommentInterface::AUTHOR, $author);
        return $this;
    }

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->setData(CommentInterface::DESCRIPTION, $description);
        return $this;
    }

    /**
     * Set Post ID
     *
     * @param string $post_id
     * @return $this
     */
    public function setPostId($post_id)
    {
        $this->setData(CommentInterface::POST_ID, $post_id);
        return $this;
    }

    /**
     * Set Rating
     *
     * @param string $rating
     * @return $this
     */
    public function setRating($rating)
    {
        $this->setData(CommentInterface::RATING, $rating);
        return $this;
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(CommentInterface::STATUS, $status);
        return $this;
    }

    /**
     * Set Date Added
     *
     * @param string $date_added
     * @return $this
     */
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
