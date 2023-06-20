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
 * @package     Mageplaza_MassOrderActions
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\MassOrderActions\Block\Adminhtml\Comment\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\MassOrderActions\Model\Config\Source\System\OrderStatus;

/**
 * Class Form
 * @package Mageplaza\MassOrderActions\Block\Adminhtml\Comment\Edit
 */
class Form extends Generic
{
    /**
     * @var OrderStatus
     */
    protected $_orderStatus;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param OrderStatus $orderStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        OrderStatus $orderStatus,
        array $data = []
    ) {
        $this->_orderStatus = $orderStatus;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'mp_comment_edit_form',
                    'action' => $this->getUrl(
                        'mpmassorderactions/order/massComment',
                        ['form_key' => $this->getFormKey()]
                    ),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );

        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');

        $fieldset = $form->addFieldset('base_fieldset', ['class' => 'fieldset-wide']);
        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Change order status to'),
            'title' => __('Change order status to'),
            'values' => $this->_orderStatus->toOptionArray()
        ]);

        $fieldset->addField('comment', 'textarea', [
            'name' => 'comment',
            'label' => __('Comments'),
            'title' => __('Comments')
        ]);

        $fieldset->addField('is_customer_notified', 'checkbox', [
            'name' => 'is_customer_notified',
            'label' => __('Send Email to Customer'),
            'title' => __('Send Email to Customer'),
            'checked' => false,
            'onchange' => 'this.value = this.checked ? 1 : 0;'
        ]);

        $fieldset->addField('is_visible_on_front', 'checkbox', [
            'name' => 'is_visible_on_front',
            'label' => __('Visible on Store Front'),
            'title' => __('Visible on Store Front'),
            'checked' => false,
            'onchange' => 'this.value = this.checked ? 1 : 0;'
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
