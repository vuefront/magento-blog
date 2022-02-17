<?php


namespace vuefront\blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Vuefront\Blog\Api\CategoryRepositoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Vuefront\Blog\Model\Uploader;
use Vuefront\Blog\Model\UploaderPool;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Vuefront\Blog\Model\Category as CategoryModel;

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
     * @var CategoryRepositoryInterface
     */
    public $categoryRepository;

    /**
     * @var CategoryInterfaceFactory
     */
    public $categoryFactory;

    /**
     * @var BaseUrlRewrite
     */
    public $urlRewrite;

    /**
     * Url rewrite service
     *
     * @var $urlRewriteService
     */
    public $urlRewriteService;

    /**
     * Url finder
     *
     * @var UrlFinderInterface
     */
    public $urlFinder;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var UrlRewriteFactory
     */
    public $urlRewriteFactory;

    private $urlPrefix;

    private $urlExtension;

    public function __construct(
        UrlFinderInterface $urlFinder,
        UrlRewriteFactory $urlRewriteFactory,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        Context $context,
        UploaderPool $uploaderPool
    ) {
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->uploaderPool = $uploaderPool;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPrefix = CategoryModel::URL_PREFIX;
        $this->urlExtension = CategoryModel::URL_EXT;

        parent::__construct($context);
    }

    public function execute()
    {
        $category = null;
        $data = $this->getRequest()->getPostValue();
        $id = !empty($data['category_id']) ? $data['category_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $category = $this->categoryRepository->getById((int)$id);
            } else {
                unset($data['category_id']);
                $category = $this->categoryFactory->create();
            }
            if ($data) {
                $category->setTitle($data['title']);
                $category->setDescription($data['description']);
                $category->setMetaTitle($data['meta_title']);
                $category->setMetaKeywords($data['meta_keywords']);
                $category->setMetaDescription($data['meta_description']);
                $category->setSortOrder($data['sort_order']);
                $category->setKeyword($data['keyword']);
                $category->setStoreId($data['store_id']);

                if (!empty($data["keyword"])) {
                    $this->saveUrlRewrite($data["keyword"], $category->getId(), $this->storeManager->getStore()->getId());
                }
                if (!empty($data['parent_id'])) {
                    $category->setParentId($data['parent_id']);
                } else {
                    $category->setParentId(0);
                }
            }
            $image = $this->getUploader('image')->uploadFileAndGetName('image', $data);
            $category->setImage($image);
            $this->categoryRepository->save($category);

            $this->messageManager->addSuccessMessage(__('You saved the Category'));

            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('vuefront_blog/category/edit', ['category_id' => $category->getId()]);
            } else {
                $resultRedirect->setPath('vuefront_blog/category');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($category != null) {
                $this->storeCategoryDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $category,
                        CategoryInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_blog/category/edit', ['category_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($category != null) {
                $this->storeCategoryDataToSession(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $category,
                        CategoryInterface::class
                    )
                );
            }
            $resultRedirect->setPath('vuefront_blog/category/edit', ['category_id' => $id]);
        }
        return $resultRedirect;
    }

    /**
     * @param $type
     * @return Uploader
     * @throws \Exception
     */
    private function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }

    /**
     * @param $categoryData
     */
    private function storeCategoryDataToSession($categoryData)
    {
        $this->_getSession()->setVuefrontBlogStoresData($categoryData);
    }
    /**
     * Saves the url rewrite for that specific store
     *
     * @param  $link string
     * @param  $id int
     * @param  $storeId int
     * @return void
     */
    private function saveUrlRewrite($link, $id, $storeId)
    {
        $getCustomUrlRewrite = $this->urlPrefix . "/" . $link.$this->urlExtension;

        $categoryId = $this->urlPrefix . "-" . $id;

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
                UrlRewriteService::ENTITY_TYPE => $categoryId,
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
                    ->setTargetPath("vuefront_blog/category/view/index")
                    ->setEntityType($categoryId)
                    ->setEntityId($id)
                    ->setIsAutogenerated(0)
                    ->save();
            }
        }
    }
}
