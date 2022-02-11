<?php

namespace Vuefront\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Vuefront\Blog\Api\Data\PostInterface;

/**
 * @api
 */
interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Stores list.
     *
     * @return PostInterface[]
     */
    public function getItems();

    /**
     * Set Stores list.
     *
     * @param  PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
