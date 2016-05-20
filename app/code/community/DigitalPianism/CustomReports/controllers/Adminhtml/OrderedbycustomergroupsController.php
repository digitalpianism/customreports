<?php

/**
 * Class DigitalPianism_CustomReports_OrderedbycustomergroupsController
 */
class DigitalPianism_CustomReports_OrderedbycustomergroupsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/orderedbycustomergroups');
    }

    /**
     *
     */
    public function indexAction()
    {
        // Get the session
        $session = Mage::getSingleton('core/session');

        // Get the dates
        $_from    = $this->getRequest()->getParam('from');
        $_to      = $this->getRequest()->getParam('to');

        // Use the session to manage the dates
        if ($_from != "") $session->setOrderedbycustomergroupsFrom($_from);
        if ($_to != "") $session->setOrderedbycustomergroupsTo($_to);

        $this->loadLayout()
            ->_setActiveMenu('customreports/orderedbycustomergroups')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Products Ordered by Customer Groups Report'), Mage::helper('adminhtml')->__('Products Ordered by Customer Groups Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/orderedbycustomergroups') )
            ->renderLayout();
    }

    /**
     * Export worstsellers report grid to CSV format
     */
    public function exportOrderedbycustomergroupsCsvAction()
    {
        $fileName   = 'orderedbycustomergroups.csv';
        $content    = $this->getLayout()->createBlock('customreports/orderedbycustomergroups_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export worstsellers report to Excel XML format
     */
    public function exportOrderedbycustomergroupsExcelAction()
    {
        $fileName   = 'orderedbycustomergroups.xml';
        $content    = $this->getLayout()->createBlock('customreports/orderedbycustomergroups_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}