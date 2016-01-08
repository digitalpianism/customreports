<?php

/**
 * Class DigitalPianism_CustomReports_NoupsellsController
 */
class DigitalPianism_CustomReports_Adminhtml_NoupsellsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/products/noupsells');
    }

    public function indexAction()
    {
        $this->loadLayout()
			 ->_setActiveMenu('customreports/noupsells')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom No Upsells Report'), Mage::helper('adminhtml')->__('Custom No Upsells Report'))
            ->_addContent( $this->getLayout()->createBlock('customreports/noupsells') )
            ->renderLayout();
    }

    /**
     * Export no upsells report grid to CSV format
     */
    public function exportNoupsellsCsvAction()
    {
        $fileName   = 'noupsells.csv';
        $content    = $this->getLayout()->createBlock('customreports/noupsells_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export no upsells report to Excel XML format
     */
    public function exportNoupsellsExcelAction()
    {
        $fileName   = 'noupsellss.xml';
        $content    = $this->getLayout()->createBlock('customreports/noupsells_grid')
            ->getExcelFile($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }
}