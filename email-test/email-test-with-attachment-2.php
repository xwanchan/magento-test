<?php

header("Content-type: text/html;charset=utf-8");
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
Mage::app();

$mailTemplate = Mage::getModel('core/email_template');
$mailTemplate->setSenderName('wendy chen');
$mailTemplate->setSenderEmail('insert@sender.email');
$mailTemplate->setTemplateSubject('Insert Subject Title');
$mailTemplate->setTemplateText('Insert Body Text');

// add attachment
$mailTemplate->getMail()->createAttachment(
	file_get_contents(Mage::getBaseDir('base') . '/var/export/export_example.csv'),
    Zend_Mime::TYPE_OCTETSTREAM,
    Zend_Mime::DISPOSITION_ATTACHMENT,
    Zend_Mime::ENCODING_BASE64,
    'export_example.csv'
);
$mailTemplate->send(
	array('a.b@c.com', 'd@e.com'),
	array('', ''),
	null
);


$uploadfilename = '';
if (!empty($_FILES["rfloorplanattachment"]["name"])) {
    $image_ext = end(explode('.',$_FILES["rfloorplanattachment"]["name"]));
    $allowed_ext = array('gif', 'png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'rtf', 'odt');
    $uploadfilename = md5(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, rand(1,100).rand(1,100))).str_replace(" ","_",$_FILES["rfloorplanattachment"]["name"]); 
    $source_upl = $_FILES["rfloorplanattachment"]["tmp_name"];
    $target_path_upl = Mage::getBaseDir('media').DS.'requestquote'.DS.$uploadfilename;  
    if (in_array($image_ext, $allowed_ext)) 
        @move_uploaded_file($source_upl, $target_path_upl);
}

$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
$templateId = 3;
$sender = array('name'=>$senderName, 'email'=>$senderEmail);
$requestquotesvars = array('firmname'=>$customer->getFirstname());

$emaiName = 'Request Quote Firms';
$storeId = Mage::app()->getStore()->getId();
$translate = Mage::getSingleton('core/translate');
$transactionalEmail = Mage::getModel('core/email_template');
if (file_exists(Mage::getBaseDir('media').DS.'requestquote'.DS.$uploadfilename)) {
	$transactionalEmail->getMail()
        ->createAttachment(
	        file_get_contents(Mage::getBaseDir('media').DS.'requestquote'.DS.$uploadfilename),
	        Zend_Mime::TYPE_OCTETSTREAM,
	        Zend_Mime::DISPOSITION_ATTACHMENT,
	        Zend_Mime::ENCODING_BASE64,
	        basename($uploadfilename)
	    );
}
$transactionalEmail->sendTransactional($templateId, $sender, $companymail, $emailName, $requestquotesvars, $storeId);
$translate->setTranslateInline(true);
unlink(Mage::getBaseDir('media').DS.'requestquote'.DS.$uploadfilename);