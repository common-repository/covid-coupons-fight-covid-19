<?php namespace CovidCoupons\Adapters\WP;

use CovidCoupons\App\Contracts\Database as DatabaseInterface;
use CovidCoupons\App\Exceptions\PaymentException;

class Database implements DatabaseInterface {

    protected $db;

    public function __construct($table)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $this->getPrefix().$table;
    }

    public function create($attributes)
    {
        // cvdapp()->debug('Creating ' . $this->table, $attributes);
        $result = $this->db->insert( $this->table, $attributes);
        if (!$result || $this->db->last_error !== '') {
            return false;
        }
        // cvdapp()->debug('Created.');
        return $this->db->insert_id;
    }

    public function update($attributes, $id)
    {
        $result = $this->db->update( $this->table, $attributes, ['id' => $id]);

        // cvdapp()->log('Updating ' . $this->table . ' where id is ' . $id, $attributes);

        if ($this->db->last_error) {
            throw new PaymentException($this->db->last_error);
        }
        return !$result || $this->db->last_error !== '' ? false : true;
    }

    public function delete($id)
    {
        $result = $this->db->delete($this->table, ['id' => $id]);
        return !$result || $this->db->last_error !== '' ? false : true;
    }

    public function exists($key, $val)
    {
        $query = $this->db->prepare("SELECT count(*) FROM {$this->table} where {$key} = %s", [$val]);
        return $this->db->get_var( $query ) > 0;
    }

    public function all($orderby = null, $order = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderby || $order) {
            list($sql, $bindings) = $this->addOrder("SELECT * FROM {$this->table}", [], $orderby, $order);
            $sql = $this->db->prepare($sql, $bindings);
        }

        return $this->db->get_results($sql, ARRAY_A);
    }

    protected function addOrder($sql, $bindings, $orderby, $order)
    {
        if ($orderby) {
            $sql .= " ORDER BY %s";
            $bindings[] = $orderby;
            if ($order) {
                $sql .= " %s";
                $bindings[] = $order;
            }
        }
        return [$sql, $bindings];
    }

    public function find($id)
    {
        $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE `id` = %d", [$id]);
        $row = $this->db->get_row($query, ARRAY_A);
        return $row;
    }

    public function findBy($key, $val, $orderby = null, $order = null)
    {
        list($sql, $bindings) = $this->addOrder("SELECT * FROM {$this->table} WHERE `{$key}` = %s", [$val], $orderby, $order);
        return $this->db->get_row($this->db->prepare($sql, $bindings), ARRAY_A);
    }

    public function getPrefix()
    {
        return $this->db->prefix;
    }
}