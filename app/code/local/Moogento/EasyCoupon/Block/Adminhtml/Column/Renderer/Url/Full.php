<?php

class Moogento_EasyCoupon_Block_Adminhtml_Column_Renderer_Url_Full extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $row->getFullUrl();
    }
}