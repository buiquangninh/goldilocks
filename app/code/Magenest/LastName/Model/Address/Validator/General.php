<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\LastName\Model\Address\Validator;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\Validator\General as CoreGeneral;

/**
 * Address general fields validator.
 */
class General extends CoreGeneral
{
    /**
     * @var \Magenest\LastName\Helper\Data
     */
    protected $_helperLastName;
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $directoryData;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magenest\LastName\Helper\Data $helperLastName
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Directory\Helper\Data $directoryData,
        \Magenest\LastName\Helper\Data $helperLastName
    )
    {
        parent::__construct($eavConfig, $directoryData);
        $this->eavConfig = $eavConfig;
        $this->directoryData = $directoryData;
        $this->_helperLastName = $helperLastName;
    }

    /**
     * @inheritdoc
     */
    public function validate(AbstractAddress $address)
    {
        $errors = array_merge(
            $this->checkRequiredFields($address),
            $this->checkOptionalFields($address)
        );
        return $errors;
    }

    /**
     * Check fields that are generally required.
     *
     * @param AbstractAddress $address
     * @return array
     * @throws \Zend_Validate_Exception
     */
    private function checkRequiredFields(AbstractAddress $address)
    {
        $errors = [];
        if (!\Zend_Validate::is($address->getFirstname(), 'NotEmpty')) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'firstname']);
        }

        if ($this->_helperLastName->isLastnameRequired() AND
            !\Zend_Validate::is($address->getLastname(), 'NotEmpty')) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'lastname']);
        }

        if (!\Zend_Validate::is($address->getStreetLine(1), 'NotEmpty')) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'street']);
        }

        if (!\Zend_Validate::is($address->getCity(), 'NotEmpty')) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'city']);
        }

        return $errors;
    }

    /**
     * Check fields that are conditionally required.
     *
     * @param AbstractAddress $address
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function checkOptionalFields(AbstractAddress $address)
    {
        $errors = [];
        if ($this->isTelephoneRequired()
            && !\Zend_Validate::is($address->getTelephone(), 'NotEmpty')
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'telephone']);
        }

        if ($this->isFaxRequired()
            && !\Zend_Validate::is($address->getFax(), 'NotEmpty')
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'fax']);
        }

        if ($this->isCompanyRequired()
            && !\Zend_Validate::is($address->getCompany(), 'NotEmpty')
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'company']);
        }

        $havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($address->getCountryId(), $havingOptionalZip)
            && !\Zend_Validate::is($address->getPostcode(), 'NotEmpty')
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'postcode']);
        }

        return $errors;
    }

    /**
     * Check if company field required in configuration.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isCompanyRequired()
    {
        return $this->eavConfig->getAttribute('customer_address', 'company')->getIsRequired();
    }

    /**
     * Check if telephone field required in configuration.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isTelephoneRequired()
    {
        return $this->eavConfig->getAttribute('customer_address', 'telephone')->getIsRequired();
    }

    /**
     * Check if fax field required in configuration.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function isFaxRequired()
    {
        return $this->eavConfig->getAttribute('customer_address', 'fax')->getIsRequired();
    }
}
