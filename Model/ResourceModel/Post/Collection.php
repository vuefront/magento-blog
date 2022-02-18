<?php

namespace Vuefront\Blog\Model\ResourceModel\Post;

use Magento\Store\Model\StoreManagerInterface;
use \Vuefront\Blog\Api\CategoryRepositoryInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var int
     */
    protected $_categoryId;

    /**
     * @var \Vuefront\Blog\Model\Category
     */
    protected $category;

    /**
     * @var CategoryRepositoryInterface|null
     */
    protected $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Vuefront\Blog\Model\Post::class,
            \Vuefront\Blog\Model\ResourceModel\Post::class
        );
        $this->_map['fields']['post_id'] = 'main_table.post_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['category'] = 'category_table.category_id';
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (is_array($field)) {
            if (count($field) > 1) {
                return parent::addFieldToFilter($field, $condition);
            } elseif (count($field) === 1) {
                $field = $field[0];
                $condition = isset($condition[0]) ? $condition[0] : $condition;
            }
        }

        if ($field === 'category_id' || $field === 'category_ids') {
            return $this->addCategoryFilter($condition);
        }

        if ($field === 'store_id' || $field === 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add category filter to collection
     * @param array|int|\Vuefront\Blog\Model\Category  $category
     * @param boolean $withAdmin
     * @return $this
     */
    public function addCategoryFilter($category, $withAdmin = true)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Vuefront\Blog\Model\Category) {
                $this->category = $category;
                $categories = [];
                $categories[] = $category->getId();
            } else {
                $categories = $category;
                if (!is_array($categories)) {
                    $categories = [$categories];
                }
            }
            $connection = $this->getConnection();
            $tableName = $this->getTable('vuefront_blog_category');
            if (is_numeric(key($categories))) {
                foreach ($categories as $k => $id) {
                    if (!is_numeric($id)) {
                        $select = $connection->select()
                            ->from(['t' => $tableName], 'category_id')
                            ->where('t.category_id = ?', $id);

                        $id = $connection->fetchOne($select);
                        if (!$id) {
                            $id = 0;
                        }

                        $categories[$k] = $id;
                    }
                }
            } else {
                $select = $connection->select()
                    ->from(['t' => $tableName], 'category_id')
                    ->where(
                        $connection->prepareSqlCondition('t.identifier', $categories)
                        . ' OR ' .
                        $connection->prepareSqlCondition('t.category_id', $categories)
                    );

                $categories = [];
                foreach ($connection->fetchAll($select) as $item) {
                    $categories[] = $item['category_id'];
                }

                if (1 === count($categories)) {
                    /* Fix for graphQL to get posts from child categories when filtering by category */
                    try {
                        $category = $this->categoryRepository->getById($categories[0]);
                        if ($category->getId()) {
                            return $this->addCategoryFilter($category);
                        }
                    } catch (\NoSuchEntityException $e) {
                    }
                }
            }

            $this->addFilter('category', ['in' => $categories], 'public');
            $this->setFlag('category_filter_added', 1);
        }

        return $this;
    }

    /**
     * Join store relation table if there is category filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('category')) {
            $this->getSelect()->join(
                ['category_table' => $this->getTable('vuefront_blog_post_category')],
                'main_table.post_id = category_table.post_id',
                []
            )->group(
                'main_table.post_id'
            );
        }
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('vuefront_blog_post_store')],
                'main_table.post_id = store_table.post_id',
                []
            )->group(
                'main_table.post_id'
            );
        }
        parent::_renderFiltersBefore();
    }
    /**
     * Add store filter to collection
     * @param array|int|\Magento\Store\Model\Store  $store
     * @param boolean $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store === null) {
            return $this;
        }

        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $this->_storeId = $store->getId();
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $this->_storeId = $store;
                $store = [$store];
            }

            if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $store)) {
                return $this;
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store', ['in' => $store], 'public');
            $this->setFlag('store_filter_added', 1);
        }
        return $this;
    }


    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('post_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('vuefront_blog_post_store')])
                ->where('cps.post_id IN (?)', $items);

            $result = [];
            foreach ($connection->fetchAll($select) as $item) {
                if (!isset($result[$item['post_id']])) {
                    $result[$item['post_id']] = [];
                }
                $result[$item['post_id']][] = $item['store_id'];
            }

            if ($result) {
                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (!isset($result[$postId])) {
                        continue;
                    }

                    if ($result[$postId] == 0) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('post_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_id', $result[$postId]);
                }
            }

            if ($this->_storeId) {
                foreach ($this as $item) {
                    $item->setStoreId($this->_storeId);
                }
            }

            $select = $connection->select()->from(['cps' => $this->getTable('vuefront_blog_post_category')])
                ->where('cps.post_id IN (?)', $items);

            $result = [];
            foreach ($connection->fetchAll($select) as $item) {
                if (!isset($result[$item['post_id']])) {
                    $result[$item['post_id']] = [];
                }
                $result[$item['post_id']][] = $item['category_id'];
            }

            if ($result) {
                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (!isset($result[$postId])) {
                        continue;
                    }

                    $item->setData('category_id', $result[$postId]);
                }
            }

            if ($this->_categoryId) {
                foreach ($this as $item) {
                    $item->setCategoryId($this->_categoryId);
                }
            }
        }

        $this->_previewFlag = false;

        return parent::_afterLoad();
    }
}
