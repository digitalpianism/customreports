<?php

/**
 * Class DigitalPianism_CustomReports_WishlistController
 */
class DigitalPianism_CustomReports_Adminhtml_WishlistController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/wishlist');
    }

    public function indexAction()
    {
		// Get the session
		$session = Mage::getSingleton('core/session');
		
		// Get the dates
		$_from    = $this->getRequest()->getParam('from');
		$_to	  = $this->getRequest()->getParam('to');
		
		// Use the session to manage the dates
		if ($_from != "") $session->setWishlistFrom($_from);
		if ($_to != "") $session->setWishlistTo($_to);
		
        $this->loadLayout()
			 ->_setActiveMenu('customreports/wishlist')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Wishlist Report'), Mage::helper('adminhtml')->__('Custom Wishlist Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/wishlist') )
            ->renderLayout();
    }

    /**
     * Export wishlist report grid to CSV format
     */
    public function exportWishlistCsvAction()
    {
        $fileName   = 'wishlist.csv';
        $content    = $this->getLayout()->createBlock('customreports/wishlist_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export wishlist report to Excel XML format
     */
    public function exportWishlistExcelAction()
    {
        $fileName   = 'wishlist.xml';
        $content    = $this->getLayout()->createBlock('customreports/wishlist_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}