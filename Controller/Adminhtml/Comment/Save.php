<?php


namespace vuefront\blog\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Vuefront\Blog\Api\CommentRepositoryInterface;
use Vuefront\Blog\Api\Data\CommentInterface;
use Vuefront\Blog\Api\Data\CommentInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

class Save extends Action
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;
    /**
     * @var CommentRepositoryInterface
     */
    public $commentRepository;

    /**
     * @var CommentInterfaceFactory
     */
    public $commentFactory;

    /**
     * Save constructor.
     *
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CommentRepositoryInterface $commentRepository
     * @param CommentInterfaceFactory $commentFactory
     * @param Context $context
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        CommentRepositoryInterface $commentRepository,
        CommentInterfaceFactory $commentFactory,
        Context $context
    ) {
        $this->commentFactory = $commentFactory;
        $this->commentRepository = $commentRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $comment = null;
        $data = $this->getRequest()->getPostValue();
        $id = !empty($data['comment_id']) ? $data['comment_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $comment = $this->commentRepository->getById((int)$id);
            } else {
                unset($data['comment_id']);
                $comment = $this->commentFactory->create();
            }
            if ($data) {
                $comment->setPostId($data['post_id']);
                $comment->setAuthor($data['author']);
                $comment->setRating($data['rating']);
                $comment->setDescription($data['description']);
                $comment->setStatus($data['status']);
            }
            $this->commentRepository->save($comment);

            $this->messageManager->addSuccessMessage(__('You saved the Comment'));

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('vuefront_blog/comment/edit', ['comment_id' => $comment->getId()]);
            } else {
                $resultRedirect->setPath('vuefront_blog/comment');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($comment != null) {
                $this->storeCommentDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $comment,
                        CommentInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_blog/comment/edit', ['comment_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($comment != null) {
                $this->storeCommentDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $comment,
                        CommentInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_blog/comment/edit', ['comment_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * Store Comment Data to Session
     *
     * @param mixed $commentData
     */
    private function storeCommentDataToSession($commentData)
    {
        $this->_getSession()->setVuefrontBlogStoresData($commentData);
    }
}
