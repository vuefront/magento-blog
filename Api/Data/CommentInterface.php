<?php
namespace Vuefront\Blog\Api\Data;

/**
 * @api
 */
interface CommentInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const COMMENT_ID           = 'comment_id';
    public const POST_ID              = 'post_id';
    public const AUTHOR               = 'author';
    public const DESCRIPTION          = 'description';
    public const RATING               = 'rating';
    public const STATUS               = 'status';
    public const DATE_ADDED           = 'date_added';
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get post id
     *
     * @return int
     */
    public function getPostId();

    /**
     * Get Author
     *
     * @return string
     */
    public function getAuthor();

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get Rating
     *
     * @return int
     */
    public function getRating();

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get Date Added
     *
     * @return string
     */
    public function getDateAdded();

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
     * @param string $postId
     * @return $this
     */
    public function setPostId($postId);

    /**
     * Set Author
     *
     * @param string $author
     * @return $this
     */
    public function setAuthor($author);

    /**
     * Set Description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set Rating
     *
     * @param string $rating
     * @return $this
     */
    public function setRating($rating);

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set Date Added
     *
     * @param string $date_added
     * @return $this
     */
    public function setDateAdded($date_added);
}
