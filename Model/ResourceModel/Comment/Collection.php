<?php

namespace Vuefront\Blog\Model\ResourceModel\Comment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Vuefront\Blog\Model\Comment::class,
            \Vuefront\Blog\Model\ResourceModel\Comment::class
        );
        $this->_map['fields']['comment_id'] = 'main_table.comment_id';
    }
}
