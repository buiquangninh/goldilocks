<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PaymentRestriction
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PaymentRestriction\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\PaymentRestriction\Block\Adminhtml\Rule\Edit\Tab\Renderer\Time;
use Mageplaza\PaymentRestriction\Helper\Data as HelperData;
use Mageplaza\PaymentRestriction\Model\Config\Source\Day;

/**
 * Class General
 * @package Mageplaza\ShippingRules\Block\Adminhtml\Rule\Edit\Tab
 */
class General extends Generic implements TabInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var Enabledisable
     */
    protected $_enableDisable;

    /**
     * @var CustomerGroup
     */
    protected $_customerGroup;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var Day
     */
    protected $_dayInWeek;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * General constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Enabledisable $enabledisable
     * @param CustomerGroup $customerGroup
     * @param DateTime $dateTime
     * @param Store $systemStore
     * @param Day $dayInWeek
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerRepositoryInterface $customerRepository,
        Enabledisable $enabledisable,
        CustomerGroup $customerGroup,
        DateTime $dateTime,
        Store $systemStore,
        Day $dayInWeek,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_customerRepository = $customerRepository;
        $this->_enableDisable = $enabledisable;
        $this->_customerGroup = $customerGroup;
        $this->_date = $dateTime;
        $this->_systemStore = $systemStore;
        $this->_dayInWeek = $dayInWeek;
        $this->_helperData = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $rule = $this->_coreRegistry->registry('mageplaza_paymentrestriction_rule');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('rule_');
        $form->setFieldNameSuffix('rule');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General'), 'class' => 'fieldset-wide']);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
            'note' => __('For internal use, this field is visible to admins.')
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->_enableDisable->toOptionArray()
        ]);
        if (!$rule->hasData('status')) {
            $rule->setStatus(1);
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store View(s)'),
                'title' => __('Store View(s)'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);

            if (!$rule->hasData('store_ids')) {
                $rule->setStoreIds(0);
            }
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('customer_group', 'multiselect', [
            'name' => 'customer_group',
            'label' => __('Customer Group(s)'),
            'title' => __('Customer Group(s)'),
            'values' => $this->_customerGroup->toOptionArray()
        ]);
        if (!$rule->hasData('customer_group')) {
            $rule->setCustomerGroup(0);
        }

        $fieldset->addField('started_at', 'date', [
            'name' => 'started_at_name',
            'label' => __('From Date'),
            'title' => __('From Date'),
            'date_format' => 'yyyy-MM-dd',
            'timezone' => false
        ]);

        $fieldset->addField('finished_at', 'date', [
            'name' => 'finished_at',
            'label' => __('To Date'),
            'title' => __('To Date'),
            'date_format' => 'yyyy-MM-dd',
            'timezone' => false
        ]);

        $fieldset->addField('day', 'multiselect', [
            'name' => 'schedule_name[day]',
            'label' => __('Select Day(s)'),
            'title' => __('Select Day(s)'),
            'values' => $this->_dayInWeek->toOptionArray()
        ]);
        if ($rule->hasData('schedule')) {
            $scheduleData = HelperData::jsonDecode($rule->getData('schedule'));
            if (isset($scheduleData['day'])) {
                $rule->setData('day', $scheduleData['day']);
            }
            if (isset($scheduleData['from_time'])) {
                $rule->setData('from_time', implode(',', $scheduleData['from_time']));
            }
            if (isset($scheduleData['to_time'])) {
                $rule->setData('to_time', implode(',', $scheduleData['to_time']));
            }
        } else {
            $rule->setData(
                'day',
                [Day::MONDAY, Day::TUESDAY, Day::WEDNESDAY, Day::THURSDAY, Day::FRIDAY, Day::SATURDAY, Day::SUNDAY]
            );
        }

        $fieldset->addField('from_time', Time::class, [
            'name' => 'schedule_name[from_time]',
            'label' => __('From Time'),
            'title' => __('From Time')
        ]);

        $fieldset->addField('to_time', Time::class, [
            'name' => 'schedule_name[to_time]',
            'label' => __('To Time'),
            'title' => __('To Time')
        ]);

        $fieldset->addField('priority', 'text', [
            'name' => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'class' => 'validate-digits-range',
            'value' => '0',
            'note' => __('If several rules meet the condition, the one with the lowest priority will be applied.'),
        ]);

        $form->addValues($rule->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
