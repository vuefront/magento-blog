<?php

namespace Vuefront\Blog\Controller\Adminhtml\Post;

use Magento\Framework\Controller\ResultFactory;
use Vuefront\Blog\Api\PostRepositoryInterface;
use Vuefront\Blog\Controller\RegistryConstants;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Post factory
     *
     * @var PostRepositoryInterface
     */
    public $postRepository;
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PostRepositoryInterface $postRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->postRepository = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vuefront_Blog::post_edit');
    }

    /**
     * Init actions.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vuefront_Blog::blog_posts')
            ->addBreadcrumb(__('Post'), __('Post'))
            ->addBreadcrumb(__('Manage Posts'), __('Manage Posts'));

        return $resultPage;
    }

    private function _initBrand()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_POST_ID, $postId);

        return $postId;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $postId = $this->_initBrand();

        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('vuefront_blog::post');
        $resultPage->getConfig()->getTitle()->prepend(__('Post'));
        $resultPage->addBreadcrumb(__('Post'), __('Post'), $this->getUrl('vuefront_blog/post'));

        if ($postId === null) {
            $resultPage->addBreadcrumb(__('New Post'), __('New Post'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Post'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Post'), __('Edit Post'));

            $resultPage->getConfig()->getTitle()->prepend(
                $this->postRepository->getById($postId)->getTitle()
            );
        }
        return $resultPage;
    }

}
