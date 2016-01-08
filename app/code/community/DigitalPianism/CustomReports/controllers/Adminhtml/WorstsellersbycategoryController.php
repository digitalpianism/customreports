<?php

/**
 * Class DigitalPianism_CustomReports_WorstsellersbycategoryController
 */
class DigitalPianism_CustomReports_Adminhtml_WorstsellersbycategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/categories/worstsellersbycategory');
    }

    public function indexAction()
    {
        // Get the session
		$session = Mage::getSingleton('core/session');
		
		// Get the dates
		$_from    = $this->getRequest()->getParam('from');
		$_to	  = $this->getRequest()->getParam('to');
		
		// Use the session to manage the dates
		if ($_from != "") $session->setFrom($_from);
		if ($_to != "") $session->setTo($_to);
		
        $this->loadLayout()
			->_setActiveMenu('customreports/worstsellersbycategory')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Worstsellers By Category Report'), Mage::helper('adminhtml')->__('Custom Worstsellers By Category Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/worstsellersbycategory') )
            ->renderLayout();
    }

    /**
     * Export worstsellersbycategory report grid to CSV format
     */
    public function exportWorstsellersbycategoryCsvAction()
    {
        $fileName   = 'worstsellersbycategory.csv';
        $content    = $this->getLayout()->createBlock('customreports/worstsellersbycategory_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export worstsellersbycategory report to Excel XML format
     */
    public function exportWorstsellersbycategoryExcelAction()
    {
        $fileName   = 'worstsellersbycategory.xml';
        $content    = $this->getLayout()->createBlock('customreports/worstsellersbycategory_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}