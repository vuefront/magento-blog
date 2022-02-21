<?php
namespace Vuefront\Blog\Model\Post;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory;
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
     * @param CollectionFactory $postCollectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        $this->pool = $pool;
        $this->collection   = $postCollectionFactory;
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
        /**
        * @var ModifierInterface $modifier
        */

        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }
}
