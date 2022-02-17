<?php

namespace Vuefront\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('vuefront_blog_post', 'post_id');
    }

    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds((int)$object->getId());
            $object->setData('store_id', $stores);
            $categories = $this->lookupCategoryIds((int)$object->getId());
            $object->setData('category_id', $categories);
        }
        return parent::_afterLoad($object);
    }


    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds((int)$object->getId());
        $newStores = (array)$object->getStoreId();

        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }

        $table = $this->getTable('vuefront_blog_post_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'post_id = ?' => (int)$object->getId(),
                'store_id IN (?)' => $delete,
            ];
            $this->getConnection()->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'post_id' => (int)$object->getId(),
                    'store_id' => (int)$storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        $oldCategories = $this->lookupCategoryIds((int)$object->getId());
        $newCategories = (array)$object->getCategoryId();

        if (empty($newCategories)) {
            $newCategories = (array)$object->getCategoryId();
        }

        $table = $this->getTable('vuefront_blog_post_category');

        $delete = array_diff($oldCategories, $newCategories);
        if ($delete) {
            $where = [
                'post_id = ?' => (int)$object->getId(),
                'category_id IN (?)' => $delete,
            ];
            $this->getConnection()->delete($table, $where);
        }

        $insert = array_diff($newCategories, $oldCategories);
        if ($insert) {
            $data = [];
            foreach ($insert as $categoryId) {
                $data[] = [
                    'post_id' => (int)$object->getId(),
                    'category_id' => (int)$categoryId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $postId
     * @return array
     */
    public function lookupCategoryIds($postId)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable('vuefront_blog_post_category'),
            'category_id'
        )->where(
            'post_id = ?',
            (int)$postId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $postId
     * @return array
     */
    public function lookupStoreIds($postId)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable('vuefront_blog_post_store'),
            'store_id'
        )->where(
            'post_id = ?',
            (int)$postId
        );

        return $adapter->fetchCol($select);
    }
}
