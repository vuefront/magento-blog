<?php
namespace Vuefront\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Vuefront\Blog\Api\Data\CommentInterface;

/**
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Save comment.
     *
     * @param  CommentInterface $comment
     * @return CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CommentInterface $comment);

    /**
     * Retrieve Comment.
     *
     * @param  int $commentId
     * @return CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $commentId);

    /**
     * Retrieve comments matching the specified criteria.
     *
     * @param  SearchCriteriaInterface $searchCriteria
     * @return \Vuefront\Blog\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Comment.
     *
     * @param  CommentInterface $comment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CommentInterface $comment);

    /**
     * Delete Comment by ID.
     *
     * @param  int $commentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($commentId);
}
