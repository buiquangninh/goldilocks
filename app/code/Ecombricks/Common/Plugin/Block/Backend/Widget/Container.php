<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Plugin\Block\Backend\Widget;

/**
 * Container plugin
 */
class Container extends \Ecombricks\Common\Plugin\View\Framework\Element\AbstractBlock
{

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function prepareLayout()
    {
        $subject = $this->getSubject();
        $toolbar = $this->getSubjectPropertyValue('toolbar');
        $buttonList = $this->getSubjectPropertyValue('buttonList');
        $toolbar->pushButtons($subject, $buttonList);
        $this->invokeSubjectParentMethod(\Magento\Framework\View\Element\AbstractBlock::class, '_prepareLayout');
        return $this;
    }

}
