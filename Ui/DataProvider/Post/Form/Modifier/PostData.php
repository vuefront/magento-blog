<?php
namespace Vuefront\Blog\Ui\DataProvider\Post\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory;

class PostData implements ModifierInterface
{
    /**
     * @var \Vuefront\Blog\Model\ResourceModel\Post\Collection
     */
    public $collection;

    /**
     * PostData constructor.
     *
     * @param CollectionFactory $postCollectionFactory
     */
    public function __construct(
        CollectionFactory $postCollectionFactory
    ) {
        $this->collection = $postCollectionFactory;
    }

    /**
     * Get Collection
     *
     * @return \Vuefront\Blog\Model\ResourceModel\Post\Collection
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
     * Modify Data
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
         * @var $post \Vuefront\Blog\Model\Post
         */
        foreach ($items as $post) {
            $_data = $post->getData();
            if (isset($_data['image'])) {
                $image = [];
                $image[0]['name'] = $post->getImage();
                $image[0]['url'] = $post->getImageUrl();
                $_data['image'] = $image;
            }
            $post->setData($_data);
            $data[$post->getId()] = $_data;
        }

        return $data;
    }
}
