<?php

/**
 * Class DigitalPianism_CustomReports_Block_Bestsellersbycategory
 */
class DigitalPianism_CustomReports_Block_Bestsellersbycategory extends DigitalPianism_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('digitalpianism/customreports/advancedgrid.phtml');
		$this->setTitle('Custom Bestsellers By Category Report');
		$this->setSideNote("<p>Attention: please note that these data may not be totally accurate due to the following reasons:</p>
							<p>- A product can be moved to a different category after an order has been placed.</p>
							<p>- A category can be deleted</p>
							<p>- A product can be in more than one category</p>
							<p>For these reasons, if a product is in several categories, it will be counted as a sale for all of these categories.</p>");
		// Set the right URL for the form which handles the dates
		$this->setFormAction(Mage::getUrl('*/*/index'));
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/bestsellersbycategory_grid', 'customreports.grid'));
        return $this;
    }

}