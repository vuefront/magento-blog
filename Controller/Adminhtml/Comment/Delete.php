<?php
namespace Vuefront\Blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vuefront\Blog\Api\CommentRepositoryInterface;
use Vuefront\Blog\Api\Data\CommentInterfaceFactory;

class Delete extends Action
{
    /**
     * @var CommentRepositoryInterface
     */
    public $commentRepository;

    /**
     * @var CommentInterfaceFactory
     */
    public $commentFactory;

    /**
     * Delete constructor.
     * @param CommentRepositoryInterface $commentRepository
     * @param CommentInterfaceFactory $commentFactory
     * @param Context $context
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        CommentInterfaceFactory $commentFactory,
        Context $context
    ) {
        $this->commentFactory = $commentFactory;
        $this->commentRepository = $commentRepository;
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
        $id = $this->getRequest()->getParam('comment_id');
        if ($id) {
            try {
                $this->commentRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Comment has been deleted.'));
                $resultRedirect->setPath('vuefront_blog/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Comment no longer exists.'));
                return $resultRedirect->setPath('vuefront_blog/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('vuefront_blog/comment/edit', ['comment_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Comment'));
                return $resultRedirect->setPath('vuefront_blog/comment/edit', ['comment_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Comment to delete.'));
        $resultRedirect->setPath('vuefront_blog/*/');
        return $resultRedirect;
    }
}
