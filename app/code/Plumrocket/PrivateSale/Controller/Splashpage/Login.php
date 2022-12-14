<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Private Sales and Flash Sales
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\PrivateSale\Controller\Splashpage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class Login extends Action
{
    /**
     * Customer session
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Url
     * @var \Magento\Customer\Model\Url
     */
    protected $url;

    /**
     * Splashpage
     * @var \Plumrocket\PrivateSale\Model\Splashpage
     */
    protected $splashpage;

    /**
     * Contructor
     * @param \Magento\Framework\App\Action\Context    $context
     * @param \Plumrocket\PrivateSale\Model\Splashpage $splashpage
     * @param \Magento\Customer\Model\Session          $customerSession
     * @param \Magento\Customer\Model\Url              $url
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Plumrocket\PrivateSale\Model\Splashpage $splashpage,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $url
    ) {
        $this->customerSession = $customerSession;
        $this->url = $url;
        $this->splashpage = $splashpage;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->customerSession->getCustomerGroupId()) {
            if ($this->splashpage->isEnabledLaunchingSoon()) {
                if ($customerId = $this->customerSession->getId()) {
                    $this->customerSession->logout()
                        ->setLastCustomerId($customerId);
                }
            } else {
                $redirectUrl = $this->url->getDashboardUrl();
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath($redirectUrl);

                return $resultRedirect;
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
