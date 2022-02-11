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
    const POST_ID               = 'post_id';
    const TITLE                = 'title';
    const SHORT_DESCRIPTION           = 'short_description';
    const DESCRIPTION         = 'description';
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
     * Get Short Description
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
     * Set Short Description
     * @param string $short_description
     * @return $this
     */
    public function setShortDescription($short_description);

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
}
