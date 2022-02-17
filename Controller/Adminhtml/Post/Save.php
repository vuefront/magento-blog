<?php


namespace vuefront\blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Vuefront\Blog\Api\Data\PostInterface;
use Vuefront\Blog\Api\Data\PostInterfaceFactory;
use Vuefront\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

class Save extends Action
{
    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;
    /**
     * @var PostRepositoryInterface
     */
    public $postRepository;

    /**
     * @var PostInterfaceFactory
     */
    public $postFactory;

    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        PostRepositoryInterface $postRepository,
        PostInterfaceFactory $postFactory,
        Context $context
    ) {
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($context);
    }

    public function execute()
    {
        $post = null;
        $data = $this->getRequest()->getPostValue();
        $id = !empty($data['post_id']) ? $data['post_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $post = $this->postRepository->getById((int)$id);
            } else {
                unset($data['post_id']);
                $post = $this->postFactory->create();
            }
            if ($data) {
                $post->setTitle($data['title']);
                $post->setDescription($data['description']);
                $post->setShortDescription($data['short_description']);
            }
            $this->postRepository->save($post);

            $this->messageManager->addSuccessMessage(__('You saved the Post'));

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('vuefront_blog/post/edit', ['post_id' => $post->getId()]);
            } else {
                $resultRedirect->setPath('vuefront_blog/post');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($post != null) {
                $this->storePostDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $post,
                        PostInterface::class
                    )
                );
            }
            $resultRedirect->setPath('brands/brands/edit', ['brand_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($post != null) {
                $this->storePostDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $post,
                        PostInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_blog/post/edit', ['brand_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * @param $postData
     */
    private function storePostDataToSession($postData)
    {
        $this->_getSession()->setVuefrontBlogStoresData($postData);
    }
}
