<?php
namespace Vuefront\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vuefront\Blog\Api\PostRepositoryInterface;
use Vuefront\Blog\Api\Data\PostInterfaceFactory;

class Delete extends Action
{
    /**
     * @var PostRepositoryInterface
     */
    public $postRepository;

    /**
     * @var PostInterfaceFactory
     */
    public $postFactory;

    /**
     * Delete constructor.
     * @param PostRepositoryInterface $postRepository
     * @param PostInterfaceFactory $postFactory
     * @param Context $context
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        PostInterfaceFactory $postFactory,
        Context $context
    ) {
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        parent::__construct($context);
    }
    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('post_id');
        if ($id) {
            try {
                $this->postRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Post has been deleted.'));
                $resultRedirect->setPath('vuefront_blog/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Post no longer exists.'));
                return $resultRedirect->setPath('vuefront_blog/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('vuefront_blog/post/edit', ['post_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Post'));
                return $resultRedirect->setPath('vuefront_blog/post/edit', ['post_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Post to delete.'));
        $resultRedirect->setPath('vuefront_blog/*/');
        return $resultRedirect;
    }
}
