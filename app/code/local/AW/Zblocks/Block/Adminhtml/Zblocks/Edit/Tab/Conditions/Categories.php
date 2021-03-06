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


class AW_Zblocks_Block_Adminhtml_Zblocks_Edit_Tab_Conditions_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    private $_afpVO = null;

    protected function _beforeToHtml()
    {
        $this->setTemplate('aw_zblocks/catalog/category/tree.phtml');
        return $this;
    }

    public function getProduct()
    {
        if (is_null($this->_afpVO))
            $this->_afpVO = new Varien_Object();
        if (!$this->_afpVO->getCategoryIds()) {
            $id = $this->getRequest()->getParam('id');
            $_data = Mage::getModel('zblocks/zblocks')->load($id);
            if (!is_object($_data))
                $_data = new Varien_Object($_data);
            if ($_data->getCategoryIds()) {
                $this->_afpVO->setCategoryIds(is_array($_data->getCategoryIds()) ? $_data->getCategoryIds() : @explode(',', $_data->getCategoryIds()));
            } else {
                $_currentlyViewed = $_data->getData('currently_viewed');
                $cats = array();
                if ($_currentlyViewed && (is_array($_currentlyViewed) || is_object($_currentlyViewed))) {
                    if (is_array($_currentlyViewed) && isset($_currentlyViewed['category_ids'])) {
                        $cats = is_array($_currentlyViewed['category_ids']) ? $_currentlyViewed['category_ids'] : explode(',', $_currentlyViewed['category_ids']);
                    } else if (is_object($_currentlyViewed) && $_currentlyViewed->getData('category_ids')) {
                        $cats = is_array($_currentlyViewed->getData('category_ids')) ? $_currentlyViewed->getData('category_ids') : explode(',', $_currentlyViewed->getData('category_ids'));
                    }
                }
                $this->_afpVO->setCategoryIds($cats);
            }
        }
        return $this->_afpVO;
    }
}
