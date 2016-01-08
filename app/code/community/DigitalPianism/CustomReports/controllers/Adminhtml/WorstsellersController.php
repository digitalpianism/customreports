<?php

/**
 * Class DigitalPianism_CustomReports_WorstsellersController
 */
class DigitalPianism_CustomReports_Adminhtml_WorstsellersController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/worstsellers');
    }

    public function indexAction()
    {
        // Get the session
		$session = Mage::getSingleton('core/session');
		
		// Get the dates
		$_from    = $this->getRequest()->getParam('from');
		$_to	  = $this->getRequest()->getParam('to');
		
		// Use the session to manage the dates
		if ($_from != "") $session->setWorstsellersFrom($_from);
		if ($_to != "") $session->setWorstsellersTo($_to);
		
        $this->loadLayout()
			->_setActiveMenu('customreports/worstsellers')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Worstsellers Report'), Mage::helper('adminhtml')->__('Custom Worstsellers Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/worstsellers') )
            ->renderLayout();
    }

    /**
     * Export worstsellers report grid to CSV format
     */
    public function exportWorstsellersCsvAction()
    {
        $fileName   = 'worstsellers.csv';
        $content    = $this->getLayout()->createBlock('customreports/worstsellers_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export worstsellers report to Excel XML format
     */
    public function exportWorstsellersExcelAction()
    {
        $fileName   = 'worstsellers.xml';
        $content    = $this->getLayout()->createBlock('customreports/worstsellers_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}