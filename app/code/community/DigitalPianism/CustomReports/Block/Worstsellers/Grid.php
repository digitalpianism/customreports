<?php

/**
 * Class DigitalPianism_CustomReports_Block_Worstsellers_Grid
 */
class DigitalPianism_CustomReports_Block_Worstsellers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $arrayBestSellers = array();

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
		$this->setPagerVisibility(false);
		$this->setFilterVisibility(false);
        $this->setId('worstsellersReportGrid');
    }

    /**
     * @param $args
     */
    public function fillBestsellersArray($args)
	{
		// Get Sku and Name
		$sku = array_key_exists('sku',$args['row']) ? $args['row']['sku'] : $args['row']['catalog_product.sku'];
		$name = array_key_exists('name',$args['row']) ? $args['row']['name'] : $args['row']['order_items_name'];
		
		// If the sku is not set
		if (!$sku)
		{
			// We get the sku
            $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addFieldToFilter('entity_id', array($args['row']['entity_id']))
                    ->addAttributeToSelect(array('sku'))
                    ->setPageSize(1);

            $sku = $collection->getFirstItem()->getSku();
			
			// If there's still no sku
			if (!$sku)
			{
				// That means the product has been deleted
				$sku = "UNKNOWN";
			}
		}
		// If the name is not set
		if (!$name)
		{
			// We get the name
            $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addFieldToFilter('entity_id', array($args['row']['entity_id']))
                    ->addAttributeToSelect(array('name'))
                    ->setPageSize(1);

            $name = $collection->getFirstItem()->getName();
			
			// If there's still no name
			if (!$name)
			{
				// That means the product has been deleted
				$name = "PRODUCT NO LONGER EXISTS";
			}
		}
		
		// We fill the array with the data
		$this->arrayBestSellers[$args['row']['entity_id']] = array(
			'sku'			=>	$sku,
			'name'			=>	$name,
			'ordered_qty'	=>	$args['row']['ordered_qty'],
			'views'			=>	0,
			'product_id'	=>	$args['row']['entity_id']
		);
	}

    /**
     * @param $args
     */
    public function addMostViewedData($args)
	{
		// If the product has been pushed to the first array
		// That means it has been sold
		if (array_key_exists($args['row']['entity_id'],$this->arrayBestSellers) && is_array($this->arrayBestSellers[$args['row']['entity_id']]))
		{
			// We get the number of views
			$this->arrayBestSellers[$args['row']['entity_id']]['views'] = $args['row']['views'];
		}
		// Else it is a product that has never been sold
		else
		{
			// Get Sku and Name
			$sku = array_key_exists('sku',$args['row']) ? $args['row']['sku'] : "";
			$name = array_key_exists('name',$args['row']) ? $args['row']['name'] : "";
			// If the sku is not set
			if (!$sku)
			{
				// We get the sku
                $collection = Mage::getResourceModel('catalog/product_collection')
                        ->addFieldToFilter('entity_id', array($args['row']['entity_id']))
                        ->addAttributeToSelect(array('sku'))
                        ->setPageSize(1);

                $sku = $collection->getFirstItem()->getSku();
			}
			// If the name is not set
			if (!$name)
			{
				// We get the name
                $collection = Mage::getResourceModel('catalog/product_collection')
                        ->addFieldToFilter('entity_id', array($args['row']['entity_id']))
                        ->addAttributeToSelect(array('name'))
                        ->setPageSize(1);

                $name = $collection->getFirstItem()->getName();
			}
			// We fill the array with the data
			$this->arrayBestSellers[$args['row']['entity_id']] = array(
				'sku'			=>	$sku,
				'name'			=>	$name,
				'ordered_qty'	=>	0,
				'views'			=>	$args['row']['views'],
				'product_id'	=>	$args['row']['entity_id']
			);
		}
	}

    /**
     * @return $this
     * @throws Exception
     * @throws Zend_Date_Exception
     */
    protected function _prepareCollection()
    {
		// Get the session
		$session = Mage::getSingleton('core/session');
		
		// Dates for one week
		$store = Mage_Core_Model_App::ADMIN_STORE_ID;
		$timezone = Mage::app()->getStore($store)->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
		date_default_timezone_set($timezone);
		
		// Automatic -30 days if no dates provided
		if ($session->getWorstsellersFrom())
		{
			$sDate = $session->getWorstsellersFrom();
		}
        else
		{
			$sDate = date('Y-m-d 00:00:00',
				Mage::getModel('core/date')->timestamp(strtotime('-30 days'))
			);
		}
		if ($session->getWorstsellersTo())
		{
			$eDate = $session->getWorstsellersTo();
		}
        else
		{
			$eDate = date('Y-m-d 23:59:59', 
				Mage::getModel('core/date')->timestamp(time())
			);
		}
		
		###############################################################################

		$start = new Zend_Date($sDate);
		$start->setTimeZone("UTC");

        $end = new Zend_Date($eDate);
		$end->setTimeZone("UTC");

		###############################################################################

		$from = $start->toString("Y-MM-dd HH:mm:ss");
		$to = $end->toString("Y-MM-dd HH:mm:ss");
		
		
		// Get the products with their ordered quantity
		$bestSellers = Mage::getResourceModel('reports/product_collection')
			->addAttributeToSelect('*')
			->addOrderedQty($from, $to)
			->setOrder('ordered_qty');
			
		$bestSellers->getSelect()->join( array ('catalog_product' => Mage::getSingleton('core/resource')->getTableName('catalog/product')), 'catalog_product.entity_id = order_items.product_id', array('catalog_product.sku'));
			
		//echo $bestSellers->printlogquery(true);

		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($bestSellers->getSelect(), array(array($this, 'fillBestsellersArray')));
			
		// Get the most viewed products
		$mostViewed = Mage::getResourceModel('reports/product_collection')
			->addAttributeToSelect('*')
			->addViewsCount($from, $to);
			
		//echo $mostViewed->printlogquery(true);
		
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($mostViewed->getSelect(), array(array($this, 'addMostViewedData')));
		
		// Obtain a list of columns to sort the array using subkeys
		$views = array();
		$qty = array();
		foreach ($this->arrayBestSellers as $key => $row) {
			// Remove the unexisting products
			if ($row['sku'] == "UNKNOWN") {
				unset($this->arrayBestSellers[$key]);
				continue;
			}
			$views[$key]  = $row['views'];
			$qty[$key] = $row['ordered_qty'];
		}

		// Sort the data with qty ascending, views descending
		// Add $arrayBestSellers as the last parameter, to sort by the common key
		array_multisort($qty, SORT_ASC, $views, SORT_DESC, $this->arrayBestSellers);
		
		// Convert the array to a collection
		$collection = new Varien_Data_Collection();
		foreach($this->arrayBestSellers as $product){
			$rowObj = new Varien_Object();
			$rowObj->setData($product);
			$collection->addItem($rowObj);
		}
		
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
         $this->addColumn('sku', array(
            'header'    => Mage::helper('reports')->__('Product SKU'),
			'width'     => '50',
            'index'     => 'sku'
        ));
		
        $this->addColumn('name', array(
            'header'    => Mage::helper('reports')->__('Product Name'),
            'width'     => '300',
            'index'     => 'name'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => Mage::helper('reports')->__('Ordered Quantity'),
            'width'     => '150',
            'index'     => 'ordered_qty',
        ));

        $this->addColumn('views', array(
            'header'    => Mage::helper('reports')->__('Views'),
            'width'     => '150',
            'index'     => 'views',
        ));
		
		$this->addColumn('action',
            array(
                'header'    =>  Mage::helper('reports')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getProductId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('reports')->__('Edit Product'),
                        'url'       => array('base'=> 'adminhtml/catalog_product/edit/'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportWorstsellersCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportWorstsellersExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }

}