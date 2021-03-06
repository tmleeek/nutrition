<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Zblocks
 * @version    2.5.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Zblocks_Block_Block extends Mage_Core_Block_Template
{
    const MODULE_NAME = 'AW_Zblocks';

    protected function _toHtml()
    {
        if(AW_Zblocks_Helper_Data::isModuleOutputDisabled()) {
            return '';
        }

        $categoryPath = array();
        $currentCategoryId = 0;

        if (
            $this->getRequest()->getControllerName() == 'category'
            && $c = $this->getRequest()->getParam('cat')
        ) {
            $categoryPath[] = $c;
            $currentCategoryId = $c;
        }

        if($product = Mage::registry('current_product')) {
            $currentCategoryId = $product->getCategoryId();
        }

        if($category = Mage::registry('current_category')) {
            if (!$currentCategoryId) {
                $currentCategoryId = $category->getEntityId();
            }
            $categoryPath = array_merge($categoryPath, explode('/', $category->getPath()));
        }

        $categoryPath = array_unique($categoryPath);

        foreach($categoryPath as $k => $v) {
            if(!$v) {
                unset($categoryPath[$k]);
            }
        }

        return implode('',
            Mage::helper('zblocks')->getBlocks(
                $this->getPosition(),
                $this->getBlockPosition(),
                $categoryPath,
                $currentCategoryId
            ));
    }
}
