<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account controller
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Customer/controllers/AccountController.php';
class Excellence_Collection_AccountController extends Mage_Customer_AccountController
{
      public function createPostAction()
        {
            $session = $this->_getSession();
            if ($session->isLoggedIn()) {
                $this->_redirect('*/*/');
                return;
            }
            $session->setEscapeMessages(true); // prevent XSS injection in user input
            if ($this->getRequest()->isPost()) {
     
                $errors = array();
     
                if (!$customer = Mage::registry('current_customer')) {
                    $customer = Mage::getModel('customer/customer')->setId(null);
                }
     
                /* @var $customerForm Mage_Customer_Model_Form */
                $customerForm = Mage::getModel('customer/form');
                $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);
     
                $customerData = $customerForm->extractData($this->getRequest());
     
                if ($this->getRequest()->getParam('is_subscribed', false)) {
                    $customer->setIsSubscribed(1);
                }
     
                /**
                 * Initialize customer group id
                 */
                $customer->getGroupId();
     
                if ($this->getRequest()->getPost('create_address')) {
                    /* @var $address Mage_Customer_Model_Address */
                    $address = Mage::getModel('customer/address');
                    /* @var $addressForm Mage_Customer_Model_Form */
                    $addressForm = Mage::getModel('customer/form');
                    $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);
     
                    $addressData    = $addressForm->extractData($this->getRequest(), 'address', false);
                    $addressErrors  = $addressForm->validateData($addressData);
     
                    if ($addressErrors === true) {
                        $address->setId(null)
                            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                        $addressForm->compactData($addressData);
                        $customer->addAddress($address);
     
                        $addressErrors = $address->validate();
                        if (is_array($addressErrors)) {
                            $errors = array_merge($errors, $addressErrors);
                        }
                    } else {
                        $errors = array_merge($errors, $addressErrors);
                    }
     
                                    // Tundra/Elbert -->
                                    if ($this->getRequest()->getPost('create_shipping_address')) {
                                            $shippingAddress = Mage::getModel('customer/address');
     
                                            $shippingAddressForm = Mage::getModel('customer/form');
                                            $shippingAddressForm->setFormCode('customer_register_address')
                                                    ->setEntity($shippingAddress);
     
                                            $shippingAddressData = array(
                                                    'firstname'  => $addressData['firstname'],
                                                    'lastname'   => $addressData['lastname'],
                                                    'company'    => $this->getRequest()->getPost('shipping_company'),
                                                    'street'     => $this->getRequest()->getPost('shipping_street'),
                                                    'city'       => $this->getRequest()->getPost('shipping_city'),
                                                    'country_id' => $this->getRequest()->getPost('shipping_country_id'),
                                                    'region'     => $this->getRequest()->getPost('shipping_region'),
                                                    'region_id'  => $this->getRequest()->getPost('shipping_region_id'),
                                                    'postcode'   => $this->getRequest()->getPost('shipping_postcode'),
                                                    'telephone'  => $this->getRequest()->getPost('shipping_telephone'),
                                                    'fax'        => $this->getRequest()->getPost('shipping_fax')
                                                    );
     
                                            $shippingAddressErrors = $addressForm->validateData($shippingAddressData);
     
                                            if ($shippingAddressErrors === true) {
                                                    $shippingAddress->setId(null)
                                                            ->setIsDefaultBilling($this->getRequest()->getParam('shipping_default_billing', false))
                                                            ->setIsDefaultShipping($this->getRequest()->getParam('shipping_default_shipping', false));
     
                                                    $shippingAddressForm->compactData($shippingAddressData);
     
                                                    $customer->addAddress($shippingAddress);
     
                                                    $shippingAddressErrors = $shippingAddress->validate();
     
                                                    if (is_array($shippingAddressErrors)) {
                                                            $errors = array_merge($errors, $shippingAddressErrors);
                                                    }
                                            } else {
                                                    $errors = array_merge($errors, $shippingAddressErrors);
                                            }
                                    }
                                    // <-- Tundra/Elbert
                }
     
                try {
                    $customerErrors = $customerForm->validateData($customerData);
                    if ($customerErrors !== true) {
                        $errors = array_merge($customerErrors, $errors);
                    } else {
                        $customerForm->compactData($customerData);
                        $customer->setPassword($this->getRequest()->getPost('password'));
                        $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                        $customerErrors = $customer->validate();
                        if (is_array($customerErrors)) {
                            $errors = array_merge($customerErrors, $errors);
                        }
                    }
     
                    $validationResult = count($errors) == 0;
     
                    if (true === $validationResult) {
                        $customer->save();
     
                        if ($customer->isConfirmationRequired()) {
                            $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl());
                            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                            $this->_redirectSuccess(Mage::getUrl('customer/index', array('_secure'=>true)));
                            return;
                        } else {
                            $session->setCustomerAsLoggedIn($customer);
                            $url = $this->_welcomeCustomer($customer);
                            $this->_redirectSuccess($url);
                            return;
                        }
                    } else {
                        $session->setCustomerFormData($this->getRequest()->getPost());
                        if (is_array($errors)) {
                            foreach ($errors as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Invalid customer data'));
                        }
                    }
                } catch (Mage_Core_Exception $e) {
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                        $url = Mage::getUrl('customer/account/forgotpassword');
                        $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                        $session->setEscapeMessages(false);
                    } else {
                        $message = $e->getMessage();
                    }
                    $session->addError($message);
                } catch (Exception $e) {
                    $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save the customer.'));
                }
            }
     
            $this->_redirectError(Mage::getUrl('*/*/create', array('_secure' => true)));
        }

}
