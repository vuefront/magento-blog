<?php

namespace Vuefront\Blog\Block\Adminhtml\Post\Edit;


use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Return the post Id.
     *
     * @return int|null
     */
    public function getPostId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
