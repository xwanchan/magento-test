<?php

header("Content-type: text/html;charset=utf-8");
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
Mage::app();

$translate = Mage::getSingleton('core/translate');
$translate->setTranslateInline(false);
try {
    $postObject = new Varien_Object();
    $postObject->setData($post);
    if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
        echo Mage::helper('contacts')->__('Please enter a valid email address. For example johndoe@domain.com.');
        exit; 
    }

    $storeId = Mage::app()->getStore()->getStoreId();
    $emailId = Mage::getStoreConfig(self::XML_PATH_SAMPLE_EMAIL_TEMPLATE);
    $mailTemplate = Mage::getModel('core/email_template');              
    $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
        ->setReplyTo($post['email'])
        ->sendTransactional($emailId, 'general', $post['email'], "Need a send to name here");

    if (!$mailTemplate->getSentSuccess()) {                 
        echo Mage::helper('contacts')->__('Unable to submit your request. Please, try again later.');
        exit;
    }               
    $translate->setTranslateInline(true);
    echo Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');

} catch (Exception $e) {
    $translate->setTranslateInline(true);
    echo Mage::helper('contacts')->__('Unable to submit your request. Please, try again later.').$e;
    exit;
}


$body = "Hi there, here is some plaintext body content";
$mail = Mage::getModel('core/email');
$mail->setToName('John Customer');
$mail->setToEmail('customer@email.com');
$mail->setBody($body);
$mail->setSubject('The Subject');
$mail->setFromEmail('yourstore@url.com');
$mail->setFromName("Your Name");
$mail->setType('text');// You can use 'html' or 'text'
try {
    $mail->send();
    Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
    $this->_redirect('');
} catch (Exception $e) {
    Mage::getSingleton('core/session')->addError('Unable to send.');
    $this->_redirect('');
}


/* use zend-email */
$to_email = '';
$to_name = 'Hello User';
$subject = ' Test Mail- CS';
$Body="Test Mail Code : "; 
$sender_email = "sender@sender.com";
$sender_name = "sender name";

$mail = new Zend_Mail();
$mail->setBodyHtml($Body);
$mail->setFrom($sender_email, $sender_name);
$mail->addTo($to_email, $to_name);
$mail->addCc($cc, $ccname);
$mail->addBCc($bcc, $bccname);
$mail->setSubject($subject);
$msg  ='';
try {
    if($mail->send()) $msg = true;
} catch(Exception $ex) {
    $msg = false;
}
Mage::helper('core')->jsonEncode($msg);
