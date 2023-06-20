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

namespace Mageplaza\Barcode\Model\System\Config\Source;

/**
 * Class PaperOrientation
 * @package Mageplaza\Barcode\Model\System\Config\Source
 */
class PaperOrientation extends OptionArray
{
    const PORTRAIT  = 'P';
    const LANDSCAPE = 'L';

    /**
     * @return array
     */
    public function getOptionHash()
    {
        return [
            self::PORTRAIT  => __('Portrait'),
            self::LANDSCAPE => __('Landscape'),
        ];
    }
}
