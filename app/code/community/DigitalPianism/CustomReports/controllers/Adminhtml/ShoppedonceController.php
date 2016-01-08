<?php

/**
 * Class DigitalPianism_CustomReports_ShoppedonceController
 */
class DigitalPianism_CustomReports_Adminhtml_ShoppedonceController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/customers/shoppedonce');
    }

    public function indexAction()
    {
        $this->loadLayout()
			 ->_setActiveMenu('customreports/shoppedonce')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Shopped Once And Never Again Report'), Mage::helper('adminhtml')->__('Custom Shopped Once And Never Again Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/shoppedonce') )
            ->renderLayout();
    }

    /**
     * Export shoppedonce report grid to CSV format
     */
    public function exportShoppedonceCsvAction()
    {
        $fileName   = 'shoppedonce.csv';
        $content    = $this->getLayout()->createBlock('customreports/shoppedonce_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export shoppedonce report to Excel XML format
     */
    public function exportShoppedonceExcelAction()
    {
        $fileName   = 'shoppedonce.xml';
        $content    = $this->getLayout()->createBlock('customreports/shoppedonce_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}