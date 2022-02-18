<?php
namespace Vuefront\Blog\Api\Data;

/**
 * @api
 */
interface PostInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const POST_ID           = 'post_id';
    public const TITLE             = 'title';
    public const SHORT_DESCRIPTION = 'short_description';
    public const DESCRIPTION       = 'description';
    public const IMAGE             = 'image';
    public const KEYWORD           = 'keyword';
    public const META_TITLE        = 'meta_title';
    public const META_KEYWORDS     = 'meta_keywords';
    public const META_DESCRIPTION  = 'meta_description';
    public const DATE_PUBLISHED    = 'date_published';
    public const DATE_ADDED        = 'date_added';
    public const DATE_MODIFIED     = 'date_modified';
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
     * Get Short Description
     *
     * @return string
     */
    public function getShortDescription();

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
     * Get Meta Description
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Get Meta Keywords
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Get Date Published
     *
     * @return string
     */
    public function getDatePublished();

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
     * Set Short Description
     *
     * @param string $short_description
     * @return $this
     */
    public function setShortDescription($short_description);

    /**
     * Set Image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image);

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
     * Set Meta Description
     *
     * @param string $meta_description
     * @return $this
     */
    public function setMetaDescription($meta_description);

    /**
     * Set Meta Keywords
     *
     * @param string $meta_keywords
     * @return $this
     */
    public function setMetaKeywords($meta_keywords);

    /**
     * Set Date Published
     *
     * @param string $data_published
     * @return $this
     */
    public function setDatePublished($data_published);

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
}
