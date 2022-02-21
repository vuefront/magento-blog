<?php
namespace Vuefront\Blog\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use \Vuefront\Blog\Model\CategoryRepository;

class Category extends Column
{
    /**
     * @var \Vuefront\Blog\Model\CategoryRepository
     */
    protected $resource;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var string
     */
    protected $categoryKey;

    /**
     * Category constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryRepository $resource
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     * @param string $categoryKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryRepository $resource,
        Escaper $escaper,
        array $components = [],
        array $data = [],
        $categoryKey = 'category_id'
    ) {
        $this->resource = $resource;
        $this->escaper = $escaper;
        $this->categoryKey = $categoryKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->categoryKey])) {
            $origCategories = $item[$this->categoryKey];
        }

        if (empty($origCategories)) {
            return '';
        }
        if (!is_array($origCategories)) {
            $origCategories = [$origCategories];
        }

        if (in_array(0, $origCategories) && count($origCategories) == 1) {
            return '';
        }

        foreach ($origCategories as $value) {
            $category = $this->resource->getById($value);
            $content .= $category->getTitle() . "<br/>";
        }

        return $content;
    }
}
