<?php

namespace Vuefront\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Vuefront\Blog\Block\Adminhtml\Post\Grid\Renderer\Action\UrlBuilder;

class CategoryActions extends Column
{
    /** Url Path */
    public const BLOG_URL_PATH_EDIT = 'vuefront_blog/category/edit';
    public const BLOG_URL_PATH_DELETE = 'vuefront_blog/category/delete';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @var string
     */
    private $deleteUrl;

    /**
     * CategoryActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     * @param string $deleteUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::BLOG_URL_PATH_EDIT,
        $deleteUrl = self::BLOG_URL_PATH_DELETE
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
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
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['category_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['category_id' => $item['category_id']]),
                        'label' => __('Edit'),
                    ];
//                    $item[$name]['delete'] = [
//                        'href' => $this->urlBuilder->getUrl(
//                              $this->deleteUrl,
//                              ['category_id' => $item['category_id']]
//                         ),
//                        'label' => __('Delete'),
//                    ];
                }
            }
        }

        return $dataSource;
    }
}
