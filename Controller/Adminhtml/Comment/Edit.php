<?php

namespace Vuefront\Blog\Controller\Adminhtml\Comment;

use Vuefront\Blog\Api\CommentRepositoryInterface;
use Vuefront\Blog\Controller\RegistryConstants;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Comment factory
     *
     * @var CommentRepositoryInterface
     */
    public $commentRepository;
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
        CommentRepositoryInterface $commentRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->commentRepository = $commentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vuefront_Blog::comment_edit');
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
        $resultPage->setActiveMenu('Vuefront_Blog::blog_comment')
            ->addBreadcrumb(__('Comment'), __('Comment'))
            ->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));

        return $resultPage;
    }

    private function _initComment()
    {
        $commentId = $this->getRequest()->getParam('comment_id');

        $this->_coreRegistry->register(RegistryConstants::CURRENT_COMMENT_ID, $commentId);

        return $commentId;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $commentId = $this->_initComment();

        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('vuefront_blog::comment');
        $resultPage->getConfig()->getTitle()->prepend(__('Comment'));
        $resultPage->addBreadcrumb(__('Comment'), __('Comment'), $this->getUrl('vuefront_blog/comment'));

        if ($commentId === null) {
            $resultPage->addBreadcrumb(__('New Comment'), __('New Comment'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Comment'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Comment'), __('Edit Comment'));

            $resultPage->getConfig()->getTitle()->prepend(
                $this->commentRepository->getById($commentId)->getAuthor()
            );
        }

        return $resultPage;
    }

}
