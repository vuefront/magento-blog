<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\PostInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
class Post extends \Magento\Framework\Model\AbstractModel implements PostInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Vuefront\Blog\Model\ResourceModel\Post::class);
    }

    public function getId()
    {
        return $this->getData(PostInterface::POST_ID);
    }
    public function getTitle()
    {
        return $this->getData(PostInterface::TITLE);
    }
    public function getShortDescription()
    {
        return $this->getData(PostInterface::SHORT_DESCRIPTION);
    }
    public function getDescription()
    {
        return $this->getData(PostInterface::DESCRIPTION);
    }
    public function getDateAdded()
    {
        return $this->getData(PostInterface::DATE_ADDED);
    }

    public function getDateModified()
    {
        return $this->getData(Post::DATE_MODIFIED);
    }

    public function setId($id)
    {
        $this->setData(PostInterface::POST_ID, $id);
        return $this;
    }
    public function setTitle($title)
    {
        $this->setData(PostInterface::TITLE, $title);
        return $this;
    }
    public function setDescription($description)
    {
        $this->setData(PostInterface::DESCRIPTION, $description);
        return $this;
    }
    public function setShortDescription($short_description)
    {
        $this->setData(PostInterface::SHORT_DESCRIPTION, $short_description);
        return $this;
    }

    public function setDateAdded($date_added)
    {
        $this->setData(PostInterface::DATE_ADDED, $date_added);
        return $this;
    }

    public function setDateModified($date_modified)
    {
        $this->setData(PostInterface::DATE_MODIFIED, $date_modified);
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['vuefront_blog_post' . '_' . $this->getId()];
    }
}
