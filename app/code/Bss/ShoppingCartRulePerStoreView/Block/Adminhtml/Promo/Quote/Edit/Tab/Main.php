<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ShoppingCartRulePerStoreView
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ShoppingCartRulePerStoreView\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Model\System\Store;

/**
 * Cart Price Rule General Information Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $salesRule;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Bss\ShoppingCartRulePerStoreView\Helper\Data
     */
    protected $helperData;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleFactory $salesRule
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleFactory $salesRule,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Bss\ShoppingCartRulePerStoreView\Helper\Data $helperData,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->salesRule = $salesRule;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_quote_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField('product_ids', 'hidden', ['name' => 'product_ids']);

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Rule Name'), 'title' => __('Rule Name'), 'required' => true]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height: 100px;'
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $field = $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name' => 'store_ids[]',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->systemStore->getStoreValuesForForm()
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
        );
        $field->setRenderer($renderer);

        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())
            ->getItems();
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' =>  $this->helperData->returnDataObject()->toOptionArray($groups, 'id', 'code')
            ]
        );

        $couponTypeFiled = $fieldset->addField(
            'coupon_type',
            'select',
            [
                'name' => 'coupon_type',
                'label' => __('Coupon'),
                'required' => true,
                'options' => $this->salesRule->create()->getCouponTypes()
            ]
        );

        $couponCodeFiled = $fieldset->addField(
            'coupon_code',
            'text',
            ['name' => 'coupon_code', 'label' => __('Coupon Code'), 'required' => true]
        );

        $autoGenerationCheckbox = $fieldset->addField(
            'use_auto_generation',
            'checkbox',
            [
                'name' => 'use_auto_generation',
                'label' => __('Use Auto Generation'),
                'note' => __('If you select and save the rule you will be able to generate multiple coupon codes.'),
                'onclick' => 'handleCouponsTabContentActivity()',
                'checked' => (int)$model->getUseAutoGeneration() > 0 ? 'checked' : ''
            ]
        );

        $autoGenerationCheckbox->setRenderer(
            $this->getLayout()->createBlock(
                \Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\Tab\Main\Renderer\Checkbox::class
            )
        );

        $usesPerCouponFiled = $fieldset->addField(
            'uses_per_coupon',
            'text',
            ['name' => 'uses_per_coupon', 'label' => __('Uses per Coupon')]
        );

        $fieldset->addField(
            'uses_per_customer',
            'text',
            ['name' => 'uses_per_customer',
                  'label' => __('Uses per Customer'),
                  'note' => __('Usage limit enforced for logged in customers only.')
            ]
        );

        $dateFormat = $this->_localeDate->getDateFormat($this->helperData->returnDateFormatShort());
        $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From'),
                'title' => __('From'),
                'input_format' => $this->helperData->returnDateFormat(),
                'date_format' => $dateFormat
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To'),
                'title' => __('To'),
                'input_format' => $this->helperData->returnDateFormat(),
                'date_format' => $dateFormat
            ]
        );

        $fieldset->addField('sort_order', 'text', ['name' => 'sort_order', 'label' => __('Priority')]);

        $fieldset->addField(
            'is_rss',
            'select',
            [
                'label' => __('Public In RSS Feed'),
                'title' => __('Public In RSS Feed'),
                'name' => 'is_rss',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );

        if (!$model->getId()) {
            //set the default value for is_rss feed to yes for new promotion
            $model->setIsRss(1);
        }

        $form->setValues($model->getData());

        $autoGenerationCheckbox->setValue(1);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        // field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                \Magento\SalesRule\Block\Widget\Form\Element\Dependence::class
            )->addFieldMap(
                $couponTypeFiled->getHtmlId(),
                $couponTypeFiled->getName()
            )->addFieldMap(
                $couponCodeFiled->getHtmlId(),
                $couponCodeFiled->getName()
            )->addFieldMap(
                $autoGenerationCheckbox->getHtmlId(),
                $autoGenerationCheckbox->getName()
            )->addFieldMap(
                $usesPerCouponFiled->getHtmlId(),
                $usesPerCouponFiled->getName()
            )->addFieldDependence(
                $couponCodeFiled->getName(),
                $couponTypeFiled->getName(),
                $this->helperData->returnCouponSpecific()
            )->addFieldDependence(
                $autoGenerationCheckbox->getName(),
                $couponTypeFiled->getName(),
                $this->helperData->returnCouponSpecific()
            )->addFieldDependence(
                $usesPerCouponFiled->getName(),
                $couponTypeFiled->getName(),
                $this->helperData->returnCouponSpecific()
            )
        );

        $this->_eventManager->dispatch('adminhtml_promo_quote_edit_tab_main_prepare_form', ['form' => $form]);

        return Generic::_prepareForm();
    }
}
