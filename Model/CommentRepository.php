<?php
namespace Vuefront\Blog\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Vuefront\Blog\Api\CommentRepositoryInterface;

use Vuefront\Blog\Api\Data\CommentInterface;
use Vuefront\Blog\Api\Data\CommentInterfaceFactory;
use Vuefront\Blog\Api\Data\CommentSearchResultsInterface;
use Vuefront\Blog\Api\Data\CommentSearchResultsInterfaceFactory;
use Vuefront\Blog\Model\ResourceModel\Comment as ResourceComment;
use Vuefront\Blog\Model\ResourceModel\Comment\Collection;
use Vuefront\Blog\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceComment
     */
    public $resource;
    /**
     * @var CommentCollectionFactory\
     */
    public $commentCollectionFactory;

    /**
     * @var CommentSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var CommentInterfaceFactory
     */
    public $commentInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * CommentRepository constructor.
     * @param ResourceComment $resource
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param CommentSearchResultsInterfaceFactory $commentSearchResultsInterfaceFactory
     * @param CommentInterfaceFactory $commentInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(
        ResourceComment $resource,
        CommentCollectionFactory $commentCollectionFactory,
        CommentSearchResultsInterfaceFactory $commentSearchResultsInterfaceFactory,
        CommentInterfaceFactory $commentInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource                  = $resource;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchResultsFactory      = $commentSearchResultsInterfaceFactory;
        $this->commentInterfaceFactory  = $commentInterfaceFactory;
        $this->dataObjectHelper          = $dataObjectHelper;
        $this->hydrator                  = $hydrator ?: ObjectManager::getInstance()
            ->get(HydratorInterface::class);
    }
    /**
     * Save comment.
     *
     * @param  CommentInterface $comment
     * @return CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CommentInterface $comment)
    {
        /**
         * @var CategoryInterface|\Magento\Framework\Model\AbstractModel $comment
         */
        try {
            $commentId = $comment->getId();
            if ($commentId && !($comment instanceof Comment && $comment->getOrigData())) {
                $comment = $this->hydrator->hydrate($this->getById($commentId), $this->hydrator->extract($comment));
            }

            $this->resource->save($comment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the comment: %1',
                    $exception->getMessage()
                )
            );
        }
        return $comment;
    }

    /**
     * Retrieve Comment.
     *
     * @param  int $commentId
     * @return CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($commentId)
    {
        if (!isset($this->instances[$commentId])) {
            /**
             * @var CommentInterface|\Magento\Framework\Model\AbstractModel $category
             */
            $comment = $this->commentInterfaceFactory->create();
            $this->resource->load($comment, $commentId);

            if (!$comment->getId()) {
                throw new NoSuchEntityException(__('Requested comment doesn\'t exist'));
            }
            $this->instances[$commentId] = $comment;
        }

        return $this->instances[$commentId];
    }

    /**
     * Retrieve comments matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return CategorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
         * @var CommentSearchResultsInterface $searchResults
         */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /**
         * @var \Vuefront\Blog\Model\ResourceModel\Comment\Collection $collection
         */
        $collection = $this->commentCollectionFactory->create();

        //Add filters from root filter group to the collection
        /**
         * @var FilterGroup $group
         */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            /**
             * @var SortOrder $sortOrder
             */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'comment_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /**
         * @var CommentInterface[] $comments
         */
        $comments = [];
        /**
         * @var \Vuefront\Blog\Model\Comment $comment
         */
        foreach ($collection as $comment) {
            /**
             * @var CommentInterface $commentDataObject
             */
            $commentDataObject = $this->commentInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $commentDataObject,
                $comment->getData(),
                CommentInterface::class
            );
            $comments[] = $commentDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($comments);
    }

    /**
     * Delete comment.
     *
     * @param  CommentInterface $comment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CommentInterface $comment)
    {
        $id = $comment->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($comment);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Comment %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Comment by ID.
     *
     * @param  int $commentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($commentId)
    {
        $comment = $this->getById($commentId);
        return $this->delete($comment);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param  FilterGroup $filterGroup
     * @param  Collection  $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    public function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
