<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab;

class Basic extends TabGeneric
{
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var \Amasty\Feed\Model\GoogleWizard
     */
    private $googleWizard;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Feed\Model\GoogleWizard $googleWizard,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Amasty\Feed\Model\RegistryContainer $registryContainer,
        array $data = []
    ) {
        $this->feldsetId = 'amfeed_basic';
        $this->googleWizard = $googleWizard;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context, $registry, $formFactory, $registryContainer, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Step 4: Basic Product Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Step 4: Basic Product Information');
    }

    /**
     * Get currencies
     *
     * @return array
     */
    protected function getCurrencyList()
    {
        $instantCurrencyFactory = $this->currencyFactory->create();
        $currencies = $instantCurrencyFactory->getConfigAllowCurrencies();

        rsort($currencies);
        $retCurrencies = array_combine($currencies, $currencies);

        return $retCurrencies;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareNotEmptyForm()
    {
        list($categoryMappingId, $feedId) = $this->getFeedStateConfiguration();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset($this->feldsetId, [
            'legend' => $this->getLegend()
        ]);

        $fieldset->addField(
            'basic',
            'text',
            [
                'name' => 'basic',
                'value' => $this->googleWizard->getBasicAttributes(),
                'label' => __('Content'),
                'title' => __('Content'),
                'note' => __('Please select attributes to output in feed')
            ]
        );

        $form->getElement('basic')
            ->setRenderer(
                $this->getLayout()->createBlock(
                    \Amasty\Feed\Block\Adminhtml\GoogleWizard\Edit\Tab\Content\Element::class
                )
            );

        if ($categoryMappingId) {
            $fieldset->addField(
                'feed_category_id',
                'hidden',
                [
                    'name' => 'feed_category_id',
                    'value' => $categoryMappingId
                ]
            );
        }

        if ($feedId) {
            $fieldset->addField(
                'feed_id',
                'hidden',
                [
                    'name'  => 'feed_id',
                    'value' => $feedId,
                ]
            );
        }

        $this->setForm($form);

        return $this;
    }
}
