<?php

/**
 * Class DigitalPianism_CustomReports_BestsellersbycategoryController
 */
class DigitalPianism_CustomReports_Adminhtml_BestsellersbycategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/categories/bestsellersbycategory');
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
			->_setActiveMenu('customreports/bestsellersbycategory')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Bestsellers By Category Report'), Mage::helper('adminhtml')->__('Custom Bestsellers By Category Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/bestsellersbycategory') )
            ->renderLayout();
    }

    /**
     * Export bestsellersbycategory report grid to CSV format
     */
    public function exportBestsellersbycategoryCsvAction()
    {
        $fileName   = 'bestsellersbycategory.csv';
        $content    = $this->getLayout()->createBlock('customreports/bestsellersbycategory_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export bestsellersbycategory report to Excel XML format
     */
    public function exportBestsellersbycategoryExcelAction()
    {
        $fileName   = 'bestsellersbycategory.xml';
        $content    = $this->getLayout()->createBlock('customreports/bestsellersbycategory_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}