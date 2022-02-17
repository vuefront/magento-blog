<?php
namespace Vuefront\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mage360\Brands\Controller\Adminhtml\Brands;
use Vuefront\Blog\Api\CategoryRepositoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterfaceFactory;

class MassDelete extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    public $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    public $categoryFactory;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('category_id');
        $data = $this->getRequest()->getPostValue();
        if (!empty($data['selected'])) {
            try {
                foreach ($data['selected'] as $value) {
                    $this->categoryRepository->deleteById($value);
                }
                $this->messageManager->addSuccessMessage(__('The Category has been deleted.'));
                $resultRedirect->setPath('vuefront_blog/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Category no longer exists.'));
                return $resultRedirect->setPath('vuefront_blog/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('vuefront_blog/category/edit', ['category_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Category'));
                return $resultRedirect->setPath('vuefront_blog/category/edit', ['category_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Category to delete.'));
        $resultRedirect->setPath('vuefront_blog/*/');
        return $resultRedirect;
    }
}
