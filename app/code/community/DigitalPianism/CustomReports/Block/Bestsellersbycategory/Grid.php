<?php

/**
 * Class DigitalPianism_CustomReports_Block_Bestsellersbycategory_Grid
 */
class DigitalPianism_CustomReports_Block_Bestsellersbycategory_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setId('bestsellersbycategoryReportGrid');
	}

    /**
     * @param $args
     * @throws Mage_Core_Exception
     */
    public function fillBestsellersArray($args)
	{
		// Get Product ID
		$id = $args['row']['entity_id'];
		
		// Get Sold Quantity and Total
		$sumQty = $args['row']['ordered_qty'];
		$sumTotal = $args['row']['ordered_total'];
		
		// Load the potential parent product ids
		$parentProduct = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
		
		// If a product is an associated product
		if (!empty($parentProduct) && isset($parentProduct[0]))
		{
			// Get the parent configurable product id
			$productId = $parentProduct[0];
		}
		else
		{	
			// Get the simple product id
			$productId = $id;
		}
		
		// Get all categories of this product
		$categories = Mage::getResourceModel('catalog/category_collection')
						->joinField('product_id',
							'catalog/category_product',
							'product_id',
							'category_id = entity_id',
							null)
						->addAttributeToSelect('name')
						->addAttributeToSelect('parent_id')
						->addFieldToFilter('product_id', $productId);
		
		// Export this collection to array so we could iterate on it's elements
		$categories = $categories->exportToArray();

		// Get categories names
		foreach($categories as $category)
		{
			// Get Category ID
			$categoryID = $category['entity_id'];
			// Get Category Name
			$categoryName = $category['name'];
			
			// If category already in the array, we add data
			if (array_key_exists($categoryID, $this->arrayBestSellers))
			{
				// We update the ordered quantity
				$this->arrayBestSellers[$categoryID]['ordered_qty'] += $sumQty;
				
				// We udpate the ordered total
				$this->arrayBestSellers[$categoryID]['ordered_total'] += $sumTotal;
			}
			else
			{				
				// Else we create a new entry with the data
				$this->arrayBestSellers[$categoryID] = array(
					'name'			=>	$categoryName,
					'ordered_qty'	=>	$sumQty,
					'ordered_total'	=>	$sumTotal
				);
			}
		}
	}

    /**
     * @return $this
     * @throws Exception
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
		if ($session->getFrom())
		{
			$sDate = $session->getFrom();
			$sDate = str_replace('/', '-', $sDate);
			$sDate = strtotime($sDate);
			$sDate = date('Y-m-d H:i:s', $sDate); 
		}
		else
		{
			$sDate = date('Y-m-d 00:00:00',
				Mage::getModel('core/date')->timestamp(strtotime('-30 days'))
			);
		}
		
		if ($session->getTo())
		{
			$eDate = $session->getTo();
			$eDate = str_replace('/', '-', $eDate);
			$eDate = strtotime($eDate);
			$eDate = date('Y-m-d H:i:s', $eDate); 
		}
		else
		{
			$eDate = date('Y-m-d 23:59:59', 
				Mage::getModel('core/date')->timestamp(time())
			);
		}
		
		// Get the bestsellers product using Magento collection
		$bestSellers = Mage::getResourceModel('reports/product_collection')
			->addOrderedQtyAndTotal($sDate, $eDate)
			->addAttributeToSelect('*');
		
		// Remove the limitation so we get all the bestsellers to be able to create accurate data
		$bestSellers->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
		
		// echo $this->getCollection()->getSelect();
		
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($bestSellers->getSelect(), array(array($this, 'fillBestsellersArray')));
		
		// Obtain a list of columns to sort the array using subkeys
		$total = array();
		$qty = array();
		foreach ($this->arrayBestSellers as $key => $row) {
			$total[$key]  = $row['ordered_total'];
			$qty[$key] = $row['ordered_qty'];
		}

		// Sort the data with qty ascending, views descending
		// Add $arrayBestSellers as the last parameter, to sort by the common key
		array_multisort($total, SORT_DESC, $qty, SORT_DESC, $this->arrayBestSellers);
		
		// Convert the array to a collection
		$collection = new Varien_Data_Collection();
		foreach($this->arrayBestSellers as $category){
			$rowObj = new Varien_Object();
			$rowObj->setData($category);
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
		 $this->addColumn('name', array(
			'header'    => Mage::helper('reports')->__('Category Name'),
			'width'     => '50',
			'index'     => 'name'
		));

		$this->addColumn('ordered_qty', array(
			'header'    => Mage::helper('reports')->__('Ordered Quantity'),
			'width'     => '150',
			'index'     => 'ordered_qty',
		));

		$this->addColumn('ordered_total', array(
			'header'    => Mage::helper('reports')->__('Ordered Total'),
			'width'     => '150',
			'index'     => 'ordered_total',
		));

		$this->addExportType('*/*/exportBestsellersbycategoryCsv', Mage::helper('reports')->__('CSV'));
		$this->addExportType('*/*/exportBestsellersbycategoryExcel', Mage::helper('reports')->__('Excel'));

		return parent::_prepareColumns();
	}
}