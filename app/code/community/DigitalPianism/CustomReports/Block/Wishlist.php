<?php

/**
 * Class DigitalPianism_CustomReports_Block_Wishlist
 */
class DigitalPianism_CustomReports_Block_Wishlist extends DigitalPianism_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('digitalpianism/customreports/advancedgrid.phtml');
		$this->setTitle('Wishlist Report');
		// Set the right URL for the form which handles the dates
		$this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/wishlist_grid', 'customreports.grid'));
        return $this;
    }

}