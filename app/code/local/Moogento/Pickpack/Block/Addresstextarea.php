<?php 
/** 
* Moogento
* 
* SOFTWARE LICENSE
* 
* This source file is covered by the Moogento End User License Agreement
* that is bundled with this extension in the file License.html
* It is also available online here:
* https://www.moogento.com/License.html
* 
* NOTICE
* 
* If you customize this file please remember that it will be overwrtitten
* with any future upgrade installs. 
* If you'd like to add a feature which is not in this software, get in touch
* at www.moogento.com for a quote.
* 
* ID          pe+sMEDTrtCzNq3pehW9DJ0lnYtgqva4i4Z=
* File        Addresstextarea.php
* @category   Moogento
* @package    pickPack
* @copyright  Copyright (c) 2014 Moogento <info@moogento.com> / All rights reserved.
* @license    https://www.moogento.com/License.html
*/ 

class Moogento_Pickpack_Block_Addresstextarea extends Moogento_Pickpack_Block_Adminhtml_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setStyle('height:6em;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $value = $element->getValue();
        } else {

            $value = '{if company}{company},|{/if company}
{if name}{name},|{/if name}
{if street}{street},|{/if street}
{if city}{city},|{/if city}
{if region}{region},|{/if region}
{if postcode}{postcode}|{/if postcode}
{if country}{country}|{/if country}
{if telephone}|[ T: {telephone} ]{/if telephone}';
        }

        $from = $element->setValue(isset($value) ? $value : null)->getElementHtml();
        return $from; //.'   '.Mage::helper('adminhtml')->__('items');
    }
}
