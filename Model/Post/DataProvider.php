<?php
namespace Vuefront\Blog\Model\Post;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    public $_loadedData;

    /**
     * @var CollectionFactory
     */
    public $collection;

    /**
     * @param string            $name
     * @param string            $primaryFieldName
     * @param string            $requestFieldName
     * @param CollectionFactory $postCollectionFactory
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $postCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
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
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->getCollection()->getItems();
        foreach ($items as $employee) {
            $this->_loadedData[$employee->getId()] = $employee->getData();
        }

        return $this->_loadedData;
    }
}
