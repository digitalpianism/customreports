<?php
/*
 *	Add a method to retrieve the bestsellers correctly
 */

/**
 * Class DigitalPianism_CustomReports_Model_Reports_Resource_Product_Collection
 */
class DigitalPianism_CustomReports_Model_Reports_Resource_Product_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addOrderedQtyAndTotal($from = '', $to = '')
    {
        $adapter              = $this->getConnection();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state = ?", Mage_Sales_Model_Order::STATE_COMPLETE),

        );

        $productJoinCondition = array('e.entity_id = order_items.product_id');

        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $this->getSelect()->reset()
            ->from(
                array('order_items' => $this->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name',
                    'order_items_sku' => 'order_items.sku',
					'ordered_total' => 'SUM(IF(order_items.base_row_total>0,order_items.base_row_total, order_items2.base_row_total))'
                ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
			->joinLeft(
                array('order_items2' => $this->getTable('sales/order_item')), "(order_items.parent_item_id = order_items2.item_id AND order_items2.product_type = 'configurable')", array())
            ->joinLeft(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->where('(order_items.parent_item_id IS NULL 
					AND order_items.product_type NOT IN ("configurable","bundle")
					) 
					OR (
					order_items.parent_item_id IS NOT NULL 
					AND order_items.product_type NOT IN ("configurable","bundle")
					)')
            ->group('e.entity_id');
            
        return $this;
    }
}
