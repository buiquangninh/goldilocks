<?php

namespace Acommerce\Gtm\Plugin;

class ShippingInformation
{
    /**
     * @var \Acommerce\Gtm\Helper\Data
     */
    protected $helper;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    /**
     * @param \Acommerce\Gtm\Helper\Data $helper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Acommerce\Gtm\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        $addressInformation
        )
    {
        $result = $proceed($cartId, $addressInformation);

        if (!$this->helper->isEnabled()) {
            return $result;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $shippingDescription = $quote->getShippingAddress()->getShippingDescription();

        $this->_checkoutSession->setCheckoutOptionsData($this->helper->addCheckoutStepPushData('1', $shippingDescription));

        return $result;
    }


}
