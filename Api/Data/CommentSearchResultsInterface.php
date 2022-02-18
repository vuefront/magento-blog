<?php

namespace Vuefront\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Vuefront\Blog\Api\Data\CommentInterface;

/**
 * @api
 */
interface CommentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Stores list.
     *
     * @return CommentInterface[]
     */
    public function getItems();

    /**
     * Set Stores list.
     *
     * @param  CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
