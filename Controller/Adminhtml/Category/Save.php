<?php


namespace vuefront\blog\Controller\Adminhtml\Category;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Vuefront\Blog\Api\CategoryRepositoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterface;
use Vuefront\Blog\Api\Data\CategoryInterfaceFactory;
use Vuefront\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Vuefront\Blog\Model\Uploader;
use Vuefront\Blog\Model\UploaderPool;


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

    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        CategoryRepositoryInterface $categoryRepository,
        CategoryInterfaceFactory $categoryFactory,
        Context $context,
        UploaderPool $uploaderPool
    )
    {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->uploaderPool = $uploaderPool;
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
}
