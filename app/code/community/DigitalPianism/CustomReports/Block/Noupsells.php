<?php

/**
 * Class DigitalPianism_CustomReports_Block_Noupsells
 */
class DigitalPianism_CustomReports_Block_Noupsells extends DigitalPianism_CustomReports_Block_Customreport
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('digitalpianism/customreports/grid.phtml');
		$this->setTitle('Custom Products With No Upsells Report');
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/noupsells_grid', 'customreports.grid'));
        return $this;
    }

}