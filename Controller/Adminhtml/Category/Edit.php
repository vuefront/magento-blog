<?php

namespace Vuefront\Blog\Controller\Adminhtml\Category;

use Vuefront\Blog\Api\CategoryRepositoryInterface;
use Vuefront\Blog\Controller\RegistryConstants;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Category factory
     *
     * @var CategoryRepositoryInterface
     */
    public $categoryRepository;
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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Is Allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vuefront_Blog::category_edit');
    }

    /**
     * Init actions.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Vuefront_Blog::blog_category')
            ->addBreadcrumb(__('Category'), __('Category'))
            ->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));

        return $resultPage;
    }

    /**
     * Init Category
     *
     * @return mixed
     */
    private function _initCategory()
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        $this->_coreRegistry->register(RegistryConstants::CURRENT_CATEGORY_ID, $categoryId);

        return $categoryId;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $categoryId = $this->_initCategory();

        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('vuefront_blog::category');
        $resultPage->getConfig()->getTitle()->prepend(__('Category'));
        $resultPage->addBreadcrumb(__('Category'), __('Category'), $this->getUrl('vuefront_blog/category'));

        if ($categoryId === null) {
            $resultPage->addBreadcrumb(__('New Category'), __('New Category'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Category'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Category'), __('Edit Category'));

            $resultPage->getConfig()->getTitle()->prepend(
                $this->categoryRepository->getById($categoryId)->getTitle()
            );
        }

        return $resultPage;
    }
}
