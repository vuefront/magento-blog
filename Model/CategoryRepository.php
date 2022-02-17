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
use Vuefront\Blog\Api\CategoryRepositoryInterface;

use Vuefront\Blog\Api\Data\CategoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterfaceFactory;
use Vuefront\Blog\Api\Data\CategorySearchResultsInterface;
use Vuefront\Blog\Api\Data\CategorySearchResultsInterfaceFactory;
use Vuefront\Blog\Model\ResourceModel\Category as ResourceCategory;
use Vuefront\Blog\Model\ResourceModel\Category\Collection;
use Vuefront\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var array
     */
    public $instances = [];
    /**
     * @var ResourceCategory
     */
    public $resource;
    /**
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;
    /**
     * @var CategorySearchResultsInterfaceFactory
     */
    public $searchResultsFactory;
    /**
     * @var CategoryInterfaceFactory
     */
    public $categoryInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    public function __construct(
        ResourceCategory $resource,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategorySearchResultsInterfaceFactory $categorySearchResultsInterfaceFactory,
        CategoryInterfaceFactory $categoryInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager,
        ?HydratorInterface $hydrator = null
    ) {
        $this->storeManager              = $storeManager;
        $this->resource                  = $resource;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultsFactory      = $categorySearchResultsInterfaceFactory;
        $this->categoryInterfaceFactory  = $categoryInterfaceFactory;
        $this->dataObjectHelper          = $dataObjectHelper;
        $this->hydrator = $hydrator ?: ObjectManager::getInstance()
            ->get(HydratorInterface::class);
    }
    /**
     * Save category.
     *
     * @param  CategoryInterface $category
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryInterface $category)
    {
        /**
         * @var CategoryInterface|\Magento\Framework\Model\AbstractModel $category
         */
        try {
            $categoryId = $category->getId();
            if ($categoryId && !($category instanceof Category && $category->getOrigData())) {
                $category = $this->hydrator->hydrate($this->getById($categoryId), $this->hydrator->extract($category));
            }

            if ($category->getStoreId() === null) {
                $storeId = $this->storeManager->getStore()->getId();
                $category->setStoreId($storeId);
            }

            $this->resource->save($category);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the category: %1',
                    $exception->getMessage()
                )
            );
        }
        return $category;
    }

    /**
     * Retrieve Category.
     *
     * @param  int $categoryId
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $categoryId)
    {
        if (!isset($this->instances[$categoryId])) {
            /**
             * @var CategoryInterface|\Magento\Framework\Model\AbstractModel $category
             */
            $category = $this->categoryInterfaceFactory->create();
            $this->resource->load($category, $categoryId);

            if (!$category->getId()) {
                throw new NoSuchEntityException(__('Requested category doesn\'t exist'));
            }
            $this->instances[$categoryId] = $category;
        }

        return $this->instances[$categoryId];
    }

    /**
     * Retrieve categories matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return CategorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
         * @var CategorySearchResultsInterface $searchResults
         */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /**
         * @var \Vuefront\Blog\Model\ResourceModel\Category\Collection $collection
         */
        $collection = $this->categoryCollectionFactory->create();

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
            $field = 'category_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /**
         * @var CategoryInterface[] $categories
         */
        $categories = [];
        /**
         * @var \Vuefront\Blog\Model\Category $category
         */
        foreach ($collection as $category) {
            /**
             * @var CategoryInterface $categoryDataObject
             */
            $categoryDataObject = $this->categoryInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($categoryDataObject, $category->getData(), CategoryInterface::class);
            $categories[] = $categoryDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($categories);
    }

    /**
     * Delete category.
     *
     * @param  CategoryInterface $category
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryInterface $category)
    {
        $id = $category->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($category);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Category %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Category by ID.
     *
     * @param  int $categoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryId)
    {
        $category = $this->getById($categoryId);
        return $this->delete($category);
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
