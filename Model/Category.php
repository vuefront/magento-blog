<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface
{

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * Url Prefix
     *
     * @var string
     */
    const URL_PREFIX = 'vuefront-blog-category';

    /**
     * Url extension
     *
     * @var string
     */
    const URL_EXT = '.html';

    public function __construct(
        UploaderPool $uploaderPool,
        Context $context,
        Registry $registry,
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        $this->uploaderPool = $uploaderPool;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Vuefront\Blog\Model\ResourceModel\Category::class);
    }

    public function getId()
    {
        return $this->getData(CategoryInterface::CATEGORY_ID);
    }
    public function getTitle()
    {
        return $this->getData(CategoryInterface::TITLE);
    }
    public function getDescription()
    {
        return $this->getData(CategoryInterface::DESCRIPTION);
    }

    public function getImage()
    {
        return $this->getData(CategoryInterface::IMAGE);
    }

    public function getParentId()
    {
        return $this->getData(CategoryInterface::PARENT_ID);
    }

    public function getKeyword()
    {
        return $this->getData(CategoryInterface::KEYWORD);
    }

    public function getMetaTitle()
    {
        return $this->getData(CategoryInterface::META_TITLE);
    }

    public function getMetaKeywords()
    {
        return $this->getData(CategoryInterface::META_KEYWORDS);
    }

    public function getMetaDescription()
    {
        return $this->getData(CategoryInterface::META_DESCRIPTION);
    }

    public function getSortOrder()
    {
        return $this->getData(CategoryInterface::SORT_ORDER);
    }

    public function getDateAdded()
    {
        return $this->getData(CategoryInterface::DATE_ADDED);
    }

    public function getDateModified()
    {
        return $this->getData(Post::DATE_MODIFIED);
    }
    /**
     *  Get Image Path url
     *
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            if (is_string($image)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$image;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    public function setId($id)
    {
        $this->setData(CategoryInterface::CATEGORY_ID, $id);
        return $this;
    }
    public function setTitle($title)
    {
        $this->setData(CategoryInterface::TITLE, $title);
        return $this;
    }
    public function setDescription($description)
    {
        $this->setData(CategoryInterface::DESCRIPTION, $description);
        return $this;
    }

    public function setImage($image)
    {
        $this->setData(CategoryInterface::IMAGE, $image);
        return $this;
    }

    public function setParentId($parentId)
    {
        $this->setData(CategoryInterface::PARENT_ID, $parentId);
        return $this;
    }

    public function setKeyword($keyword)
    {
        $this->setData(CategoryInterface::KEYWORD, $keyword);
        return $this;
    }

    public function setMetaTitle($meta_title)
    {
        $this->setData(CategoryInterface::META_TITLE, $meta_title);
        return $this;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->setData(CategoryInterface::META_KEYWORDS, $meta_keywords);
        return $this;
    }

    public function setMetaDescription($meta_description)
    {
        $this->setData(CategoryInterface::META_DESCRIPTION, $meta_description);
        return $this;
    }

    public function setSortOrder($sort_order)
    {
        $this->setData(CategoryInterface::SORT_ORDER, $sort_order);
        return $this;
    }

    public function setDateAdded($date_added)
    {
        $this->setData(CategoryInterface::DATE_ADDED, $date_added);
        return $this;
    }

    public function setDateModified($date_modified)
    {
        $this->setData(CategoryInterface::DATE_MODIFIED, $date_modified);
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return ['vuefront_blog_category' . '_' . $this->getId()];
    }
}
