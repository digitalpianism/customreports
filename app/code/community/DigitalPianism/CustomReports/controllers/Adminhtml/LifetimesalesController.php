<?php

/**
 * Class DigitalPianism_CustomReports_LifetimesalesController
 */
class DigitalPianism_CustomReports_Adminhtml_LifetimesalesController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/customers/lifetimesales');
    }

    public function indexAction()
    {
		// Get the session
		$session = Mage::getSingleton('core/session');
		
		// Get the dates
		$_from    = $this->getRequest()->getParam('from');
		$_to	  = $this->getRequest()->getParam('to');
		
		// Use the session to manage the dates
		if ($_from != "") $session->setLifetimesalesFrom($_from);
		if ($_to != "") $session->setLifetimesalesTo($_to);
		
        $this->loadLayout()
			 ->_setActiveMenu('customreports/lifetimesales')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Lifetimesales Report'), Mage::helper('adminhtml')->__('Custom Lifetimesales Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/lifetimesales') )
            ->renderLayout();
    }

    /**
     * Export lifetimesales report grid to CSV format
     */
    public function exportLifetimesalesCsvAction()
    {
        $fileName   = 'lifetimesales.csv';
        $content    = $this->getLayout()->createBlock('customreports/lifetimesales_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export lifetimesales report to Excel XML format
     */
    public function exportLifetimesalesExcelAction()
    {
        $fileName   = 'lifetimesales.xml';
        $content    = $this->getLayout()->createBlock('customreports/lifetimesales_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}