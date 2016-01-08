<?php

/**
 * Class DigitalPianism_CustomReports_Block_Worstsellers
 */
class DigitalPianism_CustomReports_Block_Worstsellers extends DigitalPianism_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('digitalpianism/customreports/advancedgrid.phtml');
		$this->setTitle('Custom Worstsellers Report');
		// Set the right URL for the form which handles the dates
		$this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/worstsellers_grid', 'customreports.grid'));
        return $this;
    }

}