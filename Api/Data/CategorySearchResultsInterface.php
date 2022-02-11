<?php

namespace Vuefront\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use Vuefront\Blog\Api\Data\CategoryInterface;

/**
 * @api
 */
interface CategorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Stores list.
     *
     * @return CategoryInterface[]
     */
    public function getItems();

    /**
     * Set Stores list.
     *
     * @param  CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
