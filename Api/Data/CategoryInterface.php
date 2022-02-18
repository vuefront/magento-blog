<?php
namespace Vuefront\Blog\Api\Data;

/**
 * @api
 */
interface CategoryInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const CATEGORY_ID      = 'category_id';
    public const TITLE            = 'title';
    public const DESCRIPTION      = 'description';
    public const IMAGE            = 'image';
    public const PARENT_ID        = 'parent_id';
    public const KEYWORD          = 'keyword';
    public const META_TITLE       = 'meta_title';
    public const META_KEYWORDS    = 'meta_keywords';
    public const META_DESCRIPTION = 'meta_description';
    public const SORT_ORDER       = 'sort_order';
    public const DATE_ADDED       = 'date_added';
    public const DATE_MODIFIED    = 'date_modified';
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get Image
     *
     * @return string
     */
    public function getImage();

    /**
     * Get Parent Id
     *
     * @return int
     */
    public function getParentId();

    /**
     * Get Keyword
     *
     * @return string
     */
    public function getKeyword();
    /**
     * Get Meta Title
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Get Meta Keywords
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Get Meta Description
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Get Sort Order
     *
     * @return int
     */
    public function getSortOrder();
    /**
     * Get Date Added
     *
     * @return string
     */
    public function getDateAdded();

    /**
     * Get Date Modified
     *
     * @return string
     */
    public function getDateModified();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * Set Parent Id
     *
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * Set Keyword
     *
     * @param string $keyword
     * @return $this
     */
    public function setKeyword($keyword);

    /**
     * Set Meta Title
     *
     * @param string $meta_title
     * @return $this
     */
    public function setMetaTitle($meta_title);

    /**
     * Set Meta Keywords
     *
     * @param string $meta_keywords
     * @return $this
     */
    public function setMetaKeywords($meta_keywords);

    /**
     * Set Meta Description
     *
     * @param string $meta_description
     * @return $this
     */
    public function setMetaDescription($meta_description);

    /**
     * Set Sort Order
     *
     * @param int $sort_order
     * @return $this
     */
    public function setSortOrder($sort_order);

    /**
     * Set Date Modified
     *
     * @param string $date_modified
     * @return $this
     */
    public function setDateModified($date_modified);

    /**
     * Set Date Added
     *
     * @param string $date_added
     * @return $this
     */
    public function setDateAdded($date_added);

    /**
     * Get Image Url
     *
     * @return bool | string
     */
    public function getImageUrl();
}
