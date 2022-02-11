<?php

namespace Vuefront\Blog\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Vuefront\Blog\Model\Category::class,
            \Vuefront\Blog\Model\ResourceModel\Category::class
        );
    }

    /**
     * Retrieve gruped category childs
     * @return array
     */
    public function getGroupedChilds()
    {
        $childs = [];
        if (count($this)) {
            foreach ($this as $item) {
                $childs[$item->getParentId()][] = $item;
            }
        }
        return $childs;
    }
}
