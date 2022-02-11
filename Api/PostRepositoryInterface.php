<?php
namespace Vuefront\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vuefront\Blog\Api\Data\PostInterface;

/**
 * @api
 */
interface PostRepositoryInterface
{
    /**
     * Save page.
     *
     * @param  PostInterface $post
     * @return PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PostInterface $post);

    /**
     * Retrieve Store.
     *
     * @param  int $postId
     * @return PostInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $postId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return \Vuefront\Blog\Api\Data\PostSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete store.
     *
     * @param  PostInterface $post
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PostInterface $post);

    /**
     * Delete Post by ID.
     *
     * @param  int $postId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($postId);
}
