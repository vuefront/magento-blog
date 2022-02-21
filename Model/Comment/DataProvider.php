<?php
namespace Vuefront\Blog\Model\Comment;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vuefront\Blog\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    public $pool;
    /**
     * @var array
     */
    public $_loadedData;

    /**
     * @var CollectionFactory
     */
    public $collection;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $commentCollectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $commentCollectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->pool = $pool;
        $this->collection   = $commentCollectionFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get Collection
     *
     * @return object
     */
    public function getCollection()
    {
        return $this->collection->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->getCollection()->getItems();
        /** @var $comment \Vuefront\Blog\Model\Comment */
        foreach ($items as $comment) {
            $this->loadedData[$comment->getId()] = $comment->getData();
        }
        return $this->loadedData;
    }
}
