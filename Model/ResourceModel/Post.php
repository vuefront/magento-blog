<?php

namespace Vuefront\Blog\Model\ResourceModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('vuefront_blog_post', 'post_id');
    }
}
