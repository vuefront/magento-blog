<?php

namespace Vuefront\Blog\Model\ResourceModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('vuefront_blog_category', 'category_id');
    }
}
