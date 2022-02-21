<?php

namespace Vuefront\Blog\Model\ResourceModel\Comment\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Vuefront\Blog\Model\ResourceModel\Comment;
use Vuefront\Blog\Model\ResourceModel\Comment\Collection as CommentCollection;

class Collection extends CommentCollection implements SearchResultInterface
{
    /**
     * Get aggregations
     *
     * @var $aggregations
     */
    protected $aggregations;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(Document::class, Comment::class);
    }

    /**
     * Get Aggregations
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Set Aggregation
     *
     * @param mixed $aggregations
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Get Search Criteria
     *
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set Search Criteria
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get Total Count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set Total Count
     *
     * @param mixed $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set Items
     *
     * @param array|null $items
     *
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
