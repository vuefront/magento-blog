<?php
namespace Vuefront\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vuefront\Blog\Api\Data\CategoryInterface;

/**
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save category.
     *
     * @param  CategoryInterface $category
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryInterface $category);

    /**
     * Retrieve Category.
     *
     * @param  int $categoryId
     * @return CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $categoryId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return \Vuefront\Blog\Api\Data\CategorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete category.
     *
     * @param  CategoryInterface $category
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryInterface $category);

    /**
     * Delete Category by ID.
     *
     * @param  int $categoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryId);
}
