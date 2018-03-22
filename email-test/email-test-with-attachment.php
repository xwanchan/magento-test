<?php

header("Content-type: text/html;charset=utf-8");
define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
Mage::app();
    	
$forceMode = true;
$mailer = Mage::getModel('core/email_template_mailer');
$emailInfo = Mage::getModel('core/email_info');
$emailInfo->addTo(array('a.b@c.com', 'd@e.com'), array());
$emailInfo->setAttachmentPath(Mage::getBaseDir('base').'/var/export/export_example.xlsx');
$emailInfo->setAttachmentName('export_example.xlsx');
$mailer->addEmailInfo($emailInfo);
$mailer->setSender(array(
    'name'=>'a b',
    'email'=>'insert@sender.email'));
$mailer->setStoreId(0);
$mailer->setTemplateId('report_department_email_template');
$mailer->setTemplateParams(array(
	'department_name'=> 'departmentName',
	'report_from'	=> '2017-12-12',
	'report_to'		=> '2017-12-12',
    )
);
$forceMode = true;
$emailQueue = Mage::getModel('core/email_queue');
$emailQueue->setEntityId($departReportId)
	->setEntityType('department')
	->setEventType('report')
	->setIsForceCheck(!$forceMode);
//$mailer->setQueue($emailQueue)->send();     


$emailTemplate = Mage::getModel('core/email_template');
$emailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>0));
if (file_exists($reportInfo->getFilePath())) {
	$emailTemplate->getMail()->createAttachment(
		file_get_contents($reportInfo->getFilePath()),
		Zend_Mime::TYPE_OCTETSTREAM,
		Zend_Mime::DISPOSITION_ATTACHMENT,
		Zend_Mime::ENCODING_BASE64,
		$reportInfo->getFileName()
	);
}
$emailTemplate->setQueue($emailQueue);
$emailTemplate->sendTransactional(
	'report_department_email_template',
    array(
        'name'=>$this->getDepartConfig('sender_name'),
        'email'=>$this->getDepartConfig('sender_email')
    ),
    explode(';', $departInfo->getEmail()),
    array(),
    array(
        'department_name'=> $departInfo->getDepartName(),
        'report_from'	=> $reportInfo->getReportDateFrom(),
        'report_to'		=> $reportInfo->getReportDateTo(),
    ), 0
);
echo '<pre>';var_dump($mailer);echo '</pre>';exit;