<?php
namespace Vuefront\Blog\Ui\DataProvider\Category\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Vuefront\Blog\Model\ResourceModel\Category\CollectionFactory;

class CategoryData implements ModifierInterface
{
    /**
     * @var \Vuefront\Blog\Model\ResourceModel\Category\Collection
     */
    public $collection;

    /**
     * CategoryData constructor.
     *
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->collection = $categoryCollectionFactory;
    }

    /**
     * Get Collection
     *
     * @return \Vuefront\Blog\Model\ResourceModel\Category\Collection
     */
    public function getCollection()
    {
        return $this->collection->create();
    }

    /**
     * Modify Meta
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Modify data
     *
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $collection = $this->getCollection();

        $items = $collection->getItems();
        /**
         * @var $category \Vuefront\Blog\Model\Category
         */
        foreach ($items as $category) {
            $_data = $category->getData();
            if (isset($_data['image'])) {
                $image = [];
                $image[0]['name'] = $category->getImage();
                $image[0]['url'] = $category->getImageUrl();
                $_data['image'] = $image;
            }
            $category->setData($_data);
            $data[$category->getId()] = $_data;
        }

        return $data;
    }
}
