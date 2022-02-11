<?php

namespace Vuefront\Blog\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Vuefront\Blog\Model\Post::class,
            \Vuefront\Blog\Model\ResourceModel\Post::class
        );
    }
}
