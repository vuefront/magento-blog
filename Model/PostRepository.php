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

use Magento\Store\Model\StoreManagerInterface;
use Vuefront\Blog\Api\PostRepositoryInterface;

use Vuefront\Blog\Api\Data\PostInterface;
use Vuefront\Blog\Api\Data\PostInterfaceFactory;
use Vuefront\Blog\Api\Data\PostSearchResultsInterface;
use Vuefront\Blog\Api\Data\PostSearchResultsInterfaceFactory;
use Vuefront\Blog\Model\ResourceModel\Post as ResourcePost;
use Vuefront\Blog\Model\ResourceModel\Post\Collection;
use Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourcePost
     */
    public $resource;
    /**
     * @var PostCollectionFactory
     */
    public $postCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var PostSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var PostInterfaceFactory
     */
    public $postInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * PostRepository constructor.
     * @param ResourcePost $resource
     * @param PostCollectionFactory $postCollectionFactory
     * @param PostSearchResultsInterfaceFactory $postSearchResultsInterfaceFactory
     * @param PostInterfaceFactory $postInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(
        ResourcePost $resource,
        PostCollectionFactory $postCollectionFactory,
        PostSearchResultsInterfaceFactory $postSearchResultsInterfaceFactory,
        PostInterfaceFactory $postInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        ?HydratorInterface $hydrator = null
    ) {
        $this->storeManager             = $storeManager;
        $this->resource                 = $resource;
        $this->postCollectionFactory    = $postCollectionFactory;
        $this->searchResultsFactory     = $postSearchResultsInterfaceFactory;
        $this->postInterfaceFactory     = $postInterfaceFactory;
        $this->dataObjectHelper         = $dataObjectHelper;
        $this->hydrator                  = $hydrator ?: ObjectManager::getInstance()
        ->get(HydratorInterface::class);
    }
    /**
     * Save page.
     *
     * @param  PostInterface $post
     * @return PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PostInterface $post)
    {
        /**
         * @var PostInterface|\Magento\Framework\Model\AbstractModel $post
         */
        try {
            $postId = $post->getId();
            if ($postId && !($post instanceof Post && $post->getOrigData())) {
                $post = $this->hydrator->hydrate($this->getById($postId), $this->hydrator->extract($post));
            }

            if ($post->getStoreId() === null) {
                $storeId = $this->storeManager->getStore()->getId();
                $post->setStoreId($storeId);
            }

            $this->resource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the post: %1',
                    $exception->getMessage()
                )
            );
        }
        return $post;
    }

    /**
     * Retrieve Post.
     *
     * @param  int $postId
     * @return PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $postId)
    {
        if (!isset($this->instances[$postId])) {
            /**
             * @var PostInterface|\Magento\Framework\Model\AbstractModel $post
             */
            $post = $this->postInterfaceFactory->create();
            $this->resource->load($post, $postId);

            if (!$post->getId()) {
                throw new NoSuchEntityException(__('Requested post doesn\'t exist'));
            }
            $this->instances[$postId] = $post;
        }

        return $this->instances[$postId];
    }

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return PostSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
         * @var PostSearchResultsInterface $searchResults
         */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /**
         * @var \Vuefront\Blog\Model\ResourceModel\Post\Collection $collection
         */
        $collection = $this->postCollectionFactory->create();

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
            $field = 'post_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /**
         * @var PostInterface[] $posts
         */
        $posts = [];
        /**
         * @var \Vuefront\Blog\Model\Post $post
         */
        foreach ($collection as $post) {
            /**
             * @var PostInterface $postDataObject
             */
            $postDataObject = $this->postInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($postDataObject, $post->getData(), PostInterface::class);
            $posts[] = $postDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($posts);
    }

    /**
     * Delete post.
     *
     * @param  PostInterface $post
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PostInterface $post)
    {
        $id = $post->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($post);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Post %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Post by ID.
     *
     * @param  int $postId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postId)
    {
        $post = $this->getById($postId);
        return $this->delete($post);
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
