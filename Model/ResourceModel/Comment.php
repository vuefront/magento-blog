<?php

namespace Vuefront\Blog\Model\ResourceModel;

class Comment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * 
     */
    protected function _construct()
    {
        $this->_init('vuefront_blog_comment', 'comment_id');
    }
}
