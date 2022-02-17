<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\PostInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * @api
 * @method Post setStoreId(int $storeId)
 * @method int getStoreId()
 * @method Post setCategoryId(int $categoryId)
 * @method int getCategoryId()
 */
class Post extends \Magento\Framework\Model\AbstractModel implements PostInterface
{

    const BASE_TMP_PATH = 'vuefront_blog/tmp/post/image';

    const BASE_PATH='vuefront_blog/post/image';

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * Url Prefix
     *
     * @var string
     */
    const URL_PREFIX = 'vuefront-blog-post';

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

    public function getImage()
    {
        return $this->getData(PostInterface::IMAGE);
    }

    public function getKeyword()
    {
        return $this->getData(PostInterface::KEYWORD);
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        $url_key = $this->getKeyword();

        return self::URL_PREFIX.'/'.$url_key.self::URL_EXT;
    }


    public function getCategories()
    {
        return $this->hasData('categories') ? $this->getData('categories') : (array)$this->getData('category_id');
    }


    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    public function getMetaTitle()
    {
        return $this->getData(PostInterface::META_TITLE);
    }

    public function getMetaDescription()
    {
        return $this->getData(PostInterface::META_DESCRIPTION);
    }

    public function getMetaKeywords()
    {
        return $this->getData(PostInterface::META_KEYWORDS);
    }

    public function getDatePublished()
    {
        return $this->getData(PostInterface::DATE_PUBLISHED);
    }

    public function getDateAdded()
    {
        return $this->getData(PostInterface::DATE_ADDED);
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
                $uploader = $this->uploaderPool->getUploader('image-post');
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

    public function setImage($image)
    {
        $this->setData(PostInterface::IMAGE, $image);
        return $this;
    }

    public function setKeyword($keyword)
    {
        $this->setData(PostInterface::KEYWORD, $keyword);
        return $this;
    }

    public function setMetaTitle($meta_title)
    {
        $this->setData(PostInterface::META_TITLE, $meta_title);
        return $this;
    }

    public function setMetaDescription($meta_description)
    {
        $this->setData(PostInterface::META_DESCRIPTION, $meta_description);
        return $this;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->setData(PostInterface::META_KEYWORDS, $meta_keywords);
        return $this;
    }

    public function setDatePublished($data_published)
    {
        $this->setData(PostInterface::DATE_PUBLISHED, $data_published);
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
