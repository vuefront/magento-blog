<?php

namespace Vuefront\Blog\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Category construcor
     */
    protected function _construct()
    {
        $this->_init('vuefront_blog_category', 'category_id');
    }

    /**
     * After Load
     *
     * @param AbstractModel $object
     * @return Category
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds((int)$object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * After Save
     *
     * @param AbstractModel $object
     * @return Category
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds((int)$object->getId());
        $newStores = (array)$object->getStoreId();

        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }

        $table = $this->getTable('vuefront_blog_category_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'category_id = ?' => (int)$object->getId(),
                'store_id IN (?)' => $delete,
            ];
            $this->getConnection()->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'category_id' => (int)$object->getId(),
                    'store_id' => (int)$storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $categoryId
     * @return array
     */
    public function lookupStoreIds($categoryId)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable('vuefront_blog_category_store'),
            'store_id'
        )->where(
            'category_id = ?',
            (int)$categoryId
        );

        return $adapter->fetchCol($select);
    }
}
