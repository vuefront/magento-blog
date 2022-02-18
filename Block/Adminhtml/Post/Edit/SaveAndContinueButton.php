<?php

namespace Vuefront\Blog\Block\Adminhtml\Post\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get Button Data
     */
    public function getButtonData()
    {
        $postId = $this->getPostId();

        $canModify = !$postId;
        $data = [];

        if ($canModify) {
            $data = [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit']
                    ]
                ],
                'sort_order' => 80
            ];
        }

        return $data;
    }
}
