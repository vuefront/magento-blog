<?php

namespace Vuefront\Blog\Controller\Adminhtml\Post;

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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PostRepositoryInterface $postRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
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
     * Is allowed
     *
     * @return bool
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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vuefront_Blog::blog_posts')
            ->addBreadcrumb(__('Post'), __('Post'))
            ->addBreadcrumb(__('Manage Posts'), __('Manage Posts'));

        return $resultPage;
    }

    /**
     * Init Post
     *
     * @return mixed
     */
    private function _initPost()
    {
        $postId = $this->getRequest()->getParam('post_id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_POST_ID, $postId);

        return $postId;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $postId = $this->_initPost();

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
