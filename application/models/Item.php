<?php
defined('BASEPATH') or exit('');

/**
 * Description of Customer
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 4th RabThaani, 1437AH (15th Jan, 2016)
 */
class Item extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getAll($orderBy, $orderFormat, $start = 0, $limit = '')
    {
        $this->db->limit($limit, $start);
        $this->db->order_by($orderBy, $orderFormat);

        $run_q = $this->db->get('items');

        if ($run_q->num_rows() > 0) {
            return $run_q->result();
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    /**
     *
     * @param type $itemName
     * @param type $itemQuantity
     * @param type $itemPrice
     * @param type $itemCost
     * @param type $itemDescription
     * @param type $itemCode
     * @return boolean
     */
    public function add(
        $itemName,
        $itemQuantity,
        $itemPrice,
        $itemCost,
        $itemDescription,
        $itemCode
    ) {
        $data = [
            'name' => $itemName,
            'quantity' => $itemQuantity,
            'unitPrice' => $itemPrice,
            'unitCost' => $itemCost,
            'description' => $itemDescription,
            'code' => $itemCode,
        ];

        //set the datetime based on the db driver in use
        $this->db->platform() == 'sqlite3'
            ? $this->db->set('dateAdded', "datetime('now')", false)
            : $this->db->set('dateAdded', 'NOW()', false);

        $this->db->insert('items', $data);

        if ($this->db->insert_id()) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function itemsearch($value)
    {
        $q =
            "SELECT * FROM items 
            WHERE 
            name LIKE '%" .
            $this->db->escape_like_str($value) .
            "%'
            || 
            code LIKE '%" .
            $this->db->escape_like_str($value) .
            "%'";

        $run_q = $this->db->query($q, [$value, $value]);

        if ($run_q->num_rows() > 0) {
            return $run_q->result();
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    /**
     * To add to the number of an item in stock
     * @param type $itemId
     * @param type $numberToadd
     * @return boolean
     */
    public function incrementItem($itemId, $numberToadd)
    {
        $q = 'UPDATE items SET quantity = quantity + ? WHERE id = ?';

        $this->db->query($q, [$numberToadd, $itemId]);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    public function decrementItem($itemCode, $numberToRemove)
    {
        $q = 'UPDATE items SET quantity = quantity - ? WHERE code = ?';

        $this->db->query($q, [$numberToRemove, $itemCode]);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    public function newstock($itemId, $qty)
    {
        $q = "UPDATE items SET quantity = quantity + $qty WHERE id = ?";

        $this->db->query($q, [$itemId]);

        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    public function deficit($itemId, $qty)
    {
        $q = "UPDATE items SET quantity = quantity - $qty WHERE id = ?";

        $this->db->query($q, [$itemId]);

        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    /**
     *
     * @param type $itemId
     * @param type $itemName
     * @param type $itemDesc
     * @param type $itemPrice
     */
    public function edit($itemId, $itemName, $itemDesc, $itemPrice, $itemCost)
    {
        $data = [
            'name' => $itemName,
            'unitPrice' => $itemPrice,
            'unitCost' => $itemCost,
            'description' => $itemDesc,
        ];

        $this->db->where('id', $itemId);
        $this->db->update('items', $data);

        return true;
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    public function getActiveItems($orderBy, $orderFormat)
    {
        $this->db->order_by($orderBy, $orderFormat);

        $this->db->where('quantity >=', 1);

        $run_q = $this->db->get('items');

        if ($run_q->num_rows() > 0) {
            return $run_q->result();
        } else {
            return false;
        }
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    /**
     * array $where_clause
     * array $fields_to_fetch
     *
     * return array | FALSE
     */
    public function getItemInfo($where_clause, $fields_to_fetch)
    {
        $this->db->select($fields_to_fetch);

        $this->db->where($where_clause);

        $run_q = $this->db->get('items');

        return $run_q->num_rows() ? $run_q->row() : false;
    }

    /*
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     ********************************************************************************************************************************
     */

    public function getItemsCumTotal()
    {
        $this->db->select('SUM(unitPrice*quantity) as cumPrice');

        $run_q = $this->db->get('items');

        return $run_q->num_rows() ? $run_q->row()->cumPrice : false;
    }
}
