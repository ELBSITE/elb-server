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
 * @package     Mageplaza_Barcode
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barcode\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;

/**
 * Class Regenerate
 * @package Mageplaza\Barcode\Model\System\Config\Backend
 */
class Regenerate extends Value
{
    /**
     * @return Value
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $this->setValue(0);
        if (is_array($value) && isset($value['regenerate'])) {
            $this->setValue(1);
        }

        return parent::beforeSave();
    }
}
