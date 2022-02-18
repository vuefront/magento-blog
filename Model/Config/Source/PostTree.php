<?php

namespace Vuefront\Blog\Model\Config\Source;

/**
 * Used in edit post form
 *
 */
class PostTree implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $_postCollectionFactory;

    /**
     * @var array
     */
    protected $_options;

    /**
     * Initialize dependencies.
     *
     * @param \Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param void
     */
    public function __construct(
        \Vuefront\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
    ) {
        $this->_postCollectionFactory = $postCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = [];
        if (count($this->options) == 0) {
            $result = $this->_postCollectionFactory->create();
            $result->load();
            $i = 0;
            foreach ($result->getItems() as $post) {
                $newLine = $i != 0 ? '<br>' : '';
                $this->options[] = [
                    "value" => $post->getId(),
                    "label" =>  html_entity_decode($newLine.$post->getTitle())
                ];
                $i++;
            }
        }
        return $this->options;
    }
}
