<?php

namespace Vuefront\Blog\Model;

use Vuefront\Blog\Api\Data\PostInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Vuefront\Blog\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Vuefront\Blog\Model\ResourceModel\Comment\Collection as CommentCollection;
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
    public const BASE_TMP_PATH = 'vuefront_blog/tmp/post/image';

    public const BASE_PATH='vuefront_blog/post/image';

    /**
     * @var CommentCollectionFactory
     */
    protected $commentCollectionFactory;

    /**
     * @var UploaderPool
     */
    public $uploaderPool;

    /**
     * @var string
     */
    public const URL_PREFIX = 'vuefront-blog-post';

    /**
     * @var string
     */
    public const URL_EXT = '.html';

    /**
     * @var CommentCollection
     */
    protected $comments = null;

    /**
     * Post constructor.
     *
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param UploaderPool $uploaderPool
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        CommentCollectionFactory $commentCollectionFactory,
        UploaderPool $uploaderPool,
        Context $context,
        Registry $registry,
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(\Vuefront\Blog\Model\ResourceModel\Post::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(PostInterface::POST_ID);
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(PostInterface::TITLE);
    }

    /**
     * Get Short Description
     *
     * @return string|null
     */
    public function getShortDescription()
    {
        return $this->getData(PostInterface::SHORT_DESCRIPTION);
    }

    /**
     * Get Description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(PostInterface::DESCRIPTION);
    }

    /**
     * Get Image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(PostInterface::IMAGE);
    }

    /**
     * Get Keyword
     *
     * @return string|null
     */
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

    /**
     * Get Categories
     *
     * @return array|mixed|null
     */
    public function getCategories()
    {
        return $this->hasData('categories') ? $this->getData('categories') : (array)$this->getData('category_id');
    }

    /**
     * Get Comments
     *
     * @param bool $enabled
     * @return CommentCollection
     */
    public function getComments($enabled = true)
    {
        if ($this->comments === null) {
            $this->comments = $this->commentCollectionFactory->create()->addFieldToFilter('post_id', $this->getId());
            if ($enabled) {
                $this->comments = $this->comments->addFieldToFilter('status', 1);
            }
        }
        return $this->comments;
    }

    /**
     * Get Rating
     *
     * @param bool $enabled
     * @return float|null
     */
    public function getRating($enabled = true)
    {
        $comments = $this->getComments($enabled);

        if (count($comments) == 0) {
            return 0;
        }

        $sum = array_sum(array_map(function ($comment) {
            return $comment->getRating();
        }, $comments->getItems()));

        return $sum / count($comments);
    }

    /**
     * Get Stores
     *
     * @return array|mixed|null
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : (array)$this->getData('store_id');
    }

    /**
     * Get Meta Title
     *
     * @return string|null
     */
    public function getMetaTitle()
    {
        return $this->getData(PostInterface::META_TITLE);
    }

    /**
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->getData(PostInterface::META_DESCRIPTION);
    }

    /**
     * Get Meta Keywords
     *
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->getData(PostInterface::META_KEYWORDS);
    }

    /**
     * Get Date Published
     *
     * @return string|null
     */
    public function getDatePublished()
    {
        return $this->getData(PostInterface::DATE_PUBLISHED);
    }

    /**
     * Get Date Added
     *
     * @return string|null
     */
    public function getDateAdded()
    {
        return $this->getData(PostInterface::DATE_ADDED);
    }

    /**
     * Get Date Modified
     *
     * @return string|null
     */
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

    /**
     * Set ID
     *
     * @param int|mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(PostInterface::POST_ID, $id);
        return $this;
    }

    /**
     * Set Title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData(PostInterface::TITLE, $title);
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
        $this->setData(PostInterface::DESCRIPTION, $description);
        return $this;
    }

    /**
     * Set Short Description
     *
     * @param string $short_description
     * @return $this
     */
    public function setShortDescription($short_description)
    {
        $this->setData(PostInterface::SHORT_DESCRIPTION, $short_description);
        return $this;
    }

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->setData(PostInterface::IMAGE, $image);
        return $this;
    }

    /**
     * Set Keyword
     *
     * @param string $keyword
     * @return $this
     */
    public function setKeyword($keyword)
    {
        $this->setData(PostInterface::KEYWORD, $keyword);
        return $this;
    }

    /**
     * Set Meta Title
     *
     * @param string $meta_title
     * @return $this
     */
    public function setMetaTitle($meta_title)
    {
        $this->setData(PostInterface::META_TITLE, $meta_title);
        return $this;
    }

    /**
     * Set Meta Description
     *
     * @param string $meta_description
     * @return $this
     */
    public function setMetaDescription($meta_description)
    {
        $this->setData(PostInterface::META_DESCRIPTION, $meta_description);
        return $this;
    }

    /**
     * Set Meta Keywords
     *
     * @param string $meta_keywords
     * @return $this
     */
    public function setMetaKeywords($meta_keywords)
    {
        $this->setData(PostInterface::META_KEYWORDS, $meta_keywords);
        return $this;
    }

    /**
     * Set Date Published
     *
     * @param string $data_published
     * @return $this
     */
    public function setDatePublished($data_published)
    {
        $this->setData(PostInterface::DATE_PUBLISHED, $data_published);
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
        $this->setData(PostInterface::DATE_ADDED, $date_added);
        return $this;
    }

    /**
     * Set Date Modified
     *
     * @param string $date_modified
     * @return $this
     */
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
