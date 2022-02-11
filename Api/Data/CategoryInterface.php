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
    const CATEGORY_ID               = 'category_id';
    const TITLE                = 'title';
    const DESCRIPTION         = 'description';
    const IMAGE = 'image';
    const PARENT_ID = 'parent_id';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const DATE_ADDED = 'date_added';
    const DATE_MODIFIED = 'date_modified';
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
     * @return string
     */
    public function getImage();

    /**
     * Get Parent Id
     * @return int
     */
    public function getParentId();

    /**
     * Get Meta Title
     * @return string
     */
    public function getMetaTitle();

    /**
     * Get Meta Keywords
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Get Meta Description
     * @return string
     */
    public function getMetaDescription();
    /**
     * Get Date Added
     * @return string
     */
    public function getDateAdded();

    /**
     * Get Date Modified
     * @return string
     */
    public function getDateModified();

    /**
     * set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * set title
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
     * @param string $image
     * @return $this
     */
    public function setImage($image);

    /**
     * @param int $parentId
     * @return $this
     */
    public function setParentId($parentId);

    /**
     * @param string $meta_title
     * @return $this
     */
    public function setMetaTitle($meta_title);

    /**
     * @param string $meta_keywords
     * @return $this
     */
    public function setMetaKeywords($meta_keywords);

    /**
     * @param string $meta_description
     * @return $this
     */
    public function setMetaDescription($meta_description);

    /**
     * Set Date Modified
     * @param string $date_modified
     * @return $this
     */
    public function setDateModified($date_modified);

    /**
     * Set Date Added
     * @param string $date_added
     * @return $this
     */
    public function setDateAdded($date_added);

    /**
     * @return bool | string
     */
    public function getImageUrl();
}
