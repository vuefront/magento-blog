<?php

namespace Vuefront\Blog\Ui\Component\Listing\Column;

use Magento\Framework\DB\Helper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;

class Websites extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    public const NAME = 'websites';

    /**
     * @var string
     */
    private $websiteNames = 'website_names';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    private $resourceHelper;

    /**
     * Websites constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     * @param Helper|null $resourceHelper
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
        Helper $resourceHelper = null
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->storeManager = $storeManager;
        $this->resourceHelper = $resourceHelper ?: $objectManager->get(Helper::class);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $websiteNames = [];

        foreach ($this->getData('options') as $website) {
            $websiteNames[$website->getWebsiteId()] = $website->getName();
        }
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $websites = [];
                foreach ($item[$fieldName] as $websiteId) {
                    if (!isset($websiteNames[$websiteId])) {
                        continue;
                    }
                    $websites[] = $websiteNames[$websiteId];
                }
                $item[$fieldName] = implode(', ', $websites);
            }
        }

        return $dataSource;
    }

    /**
     * Prepare component configuration.
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }

    /**
     * Apply sorting.
     *
     * @return void
     * @since 103.0.2
     */
    protected function applySorting()
    {
        $sorting = $this->getContext()->getRequestParam('sorting');
        $isSortable = $this->getData('config/sortable');
        if ($isSortable !== false
            && !empty($sorting['field'])
            && !empty($sorting['direction'])
            && $sorting['field'] === $this->getName()
            && in_array(strtoupper($sorting['direction']), ['ASC', 'DESC'], true)
        ) {
            /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection */
            $collection = $this->getContext()->getDataProvider()->getCollection();

            $select = $collection->getConnection()->select();
            $select->from(
                ['cpw' => $collection->getTable('catalog_product_website')],
                ['product_id']
            )->joinLeft(
                ['sw' => $collection->getTable('store_website')],
                'cpw.website_id = sw.website_id',
                [
                    $this->websiteNames => new \Zend_Db_Expr(
                        'GROUP_CONCAT(sw.name ORDER BY sw.website_id ASC SEPARATOR \',\')'
                    )
                ]
            )->group(
                'cpw.product_id'
            );

            $collection->getSelect()->joinLeft(
                ['product_websites' => $select],
                'product_websites.product_id = e.entity_id',
                [$this->websiteNames]
            )->order(
                'product_websites.' . $this->websiteNames . ' ' . $sorting['direction']
            );
        }
    }
}
