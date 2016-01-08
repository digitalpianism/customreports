<?php

/**
 * Class DigitalPianism_CustomReports_SignedupnoorderController
 */
class DigitalPianism_CustomReports_Adminhtml_SignedupnoorderController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/customers/signedupnoorder');
    }

    public function indexAction()
    {
        $this->loadLayout()
			 ->_setActiveMenu('customreports/signedupnoorder')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Signed Up But Never Shopped Report'), Mage::helper('adminhtml')->__('Custom Signed Up But Never Shopped Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/signedupnoorder') )
            ->renderLayout();
    }

    /**
     * Export signedupnoorder report grid to CSV format
     */
    public function exportSignedupnoorderCsvAction()
    {
        $fileName   = 'signedupnoorder.csv';
        $content    = $this->getLayout()->createBlock('customreports/signedupnoorder_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export signedupnoorder report to Excel XML format
     */
    public function exportSignedupnoorderExcelAction()
    {
        $fileName   = 'signedupnoorder.xml';
        $content    = $this->getLayout()->createBlock('customreports/signedupnoorder_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}