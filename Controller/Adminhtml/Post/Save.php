<?php


namespace vuefront\blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Vuefront\Blog\Api\PostRepositoryInterface;
use Vuefront\Blog\Api\Data\PostInterfaceFactory;
use Vuefront\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Vuefront\Blog\Model\Uploader;
use Vuefront\Blog\Model\UploaderPool;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Vuefront\Blog\Model\Post as PostModel;

class Save extends Action
{
    /**
     * @var UploaderPool
     */
    public $uploaderPool;

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

    /**
     * @var BaseUrlRewrite
     */
    public $urlRewrite;

    /**
     * @var UrlRewriteService
     */
    public $urlRewriteService;

    /**
     * @var UrlFinderInterface
     */
    public $urlFinder;

    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var UrlRewriteFactory
     */
    public $urlRewriteFactory;

    /**
     * @var string
     */
    private $urlPrefix;

    /**
     * @var string
     */
    private $urlExtension;

    /**
     * Save constructor.
     *
     * @param UrlFinderInterface $urlFinder
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param BaseUrlRewrite $urlRewrite
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param PostRepositoryInterface $postRepository
     * @param PostInterfaceFactory $postFactory
     * @param Context $context
     * @param UploaderPool $uploaderPool
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        UrlRewriteFactory $urlRewriteFactory,
        BaseUrlRewrite $urlRewrite,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        PostRepositoryInterface $postRepository,
        PostInterfaceFactory $postFactory,
        Context $context,
        UploaderPool $uploaderPool
    ) {
        $this->urlRewrite = $urlRewrite;
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->uploaderPool = $uploaderPool;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPrefix = PostModel::URL_PREFIX;
        $this->urlExtension = PostModel::URL_EXT;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
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
                $post->setShortDescription($data['short_description']);
                $post->setDescription($data['description']);
                $post->setMetaTitle($data['meta_title']);
                $post->setMetaKeywords($data['meta_keywords']);
                $post->setMetaDescription($data['meta_description']);
                $post->setKeyword($data['keyword']);
                $post->setStoreId($data['store_id']);
                $post->setCategoryId($data['category_id']);
                $post->setDatePublished($data['date_published']);

                if (!empty($data["keyword"])) {
                    $this->saveUrlRewrite($data["keyword"], $post->getId(), $this->storeManager->getStore()->getId());
                }
            }
            $image = $this->getUploader('image-post')->uploadFileAndGetName('image', $data);

            $post->setImage($image);
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
            $resultRedirect->setPath('vuefront_blog/post/edit', ['post_id' => $id]);
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
            $resultRedirect->setPath('vuefront_blog/post/edit', ['post_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * Get Uploader
     *
     * @param string $type
     * @return Uploader
     * @throws \Exception
     */
    private function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }

    /**
     * Store Post Data To Session
     *
     * @param mixed $categoryData
     */
    private function storePostDataToSession($categoryData)
    {
        $this->_getSession()->setVuefrontBlogStoresData($categoryData);
    }
    /**
     * Saves the url rewrite for that specific store
     *
     * @param string $link
     * @param int $id
     * @param int $storeId
     * @return void
     */
    private function saveUrlRewrite($link, $id, $storeId)
    {
        $getCustomUrlRewrite = $this->urlPrefix . "/" . $link.$this->urlExtension;

        $postId = $this->urlPrefix . "-" . $id;

        $filterData = [
            UrlRewriteService::STORE_ID => $storeId,
            UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
            UrlRewriteService::ENTITY_ID => $id,

        ];

        // check if there is an entity with same url and same id
        $rewriteFinder = $this->urlFinder->findOneByData($filterData);

        // if there is then do nothing, otherwise proceed
        if ($rewriteFinder === null) {
            // check maybe there is an old url with this target path and delete it
            $filterDataOldUrl = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
            ];
            $rewriteFinderOldUrl = $this->urlFinder->findOneByData($filterDataOldUrl);

            if ($rewriteFinderOldUrl !== null) {
                $this->urlRewrite->load($rewriteFinderOldUrl->getUrlRewriteId())->delete();
            }

            // check maybe there is an old id with different url, in this case load the id and update the url
            $filterDataOldId = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::ENTITY_TYPE => $postId,
                UrlRewriteService::ENTITY_ID => $id
            ];
            $rewriteFinderOldId = $this->urlFinder->findOneByData($filterDataOldId);

            if ($rewriteFinderOldId !== null) {
                $this->urlRewriteFactory->create()->load($rewriteFinderOldId->getUrlRewriteId())
                    ->setRequestPath($getCustomUrlRewrite)
                    ->save();
            } else {
                // now we can save
                $this->urlRewriteFactory->create()
                    ->setStoreId($storeId)
                    ->setIdPath(rand(1, 100000))
                    ->setRequestPath($getCustomUrlRewrite)
                    ->setTargetPath("vuefront_blog/post/view/index")
                    ->setEntityType($postId)
                    ->setEntityId($id)
                    ->setIsAutogenerated(0)
                    ->save();
            }
        }
    }
}
