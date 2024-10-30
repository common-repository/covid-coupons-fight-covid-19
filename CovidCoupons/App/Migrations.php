<?php namespace CovidCoupons\App;

use CovidCoupons\App\Contracts\DatabaseSchema;
use CovidCoupons\App\Contracts\GiftCertificate;
use CovidCoupons\App\Contracts\Migrations as MigrationsInterface;
use CovidCoupons\App\SqlManager;

class Migrations implements MigrationsInterface {

    private $schema;
    private $sql;

    public function __construct(SqlManager $sql, DatabaseSchema $schema)
    {
        $this->schema = $schema;
        $this->sql = $sql;
    }

    protected function getTableName()
    {
        $c = cvdapp()->container->getResolver(GiftCertificate::class);
        return $c::getTable();
    }

    public function createTable()
    {
        $table = $this->prefix().$this->getTableName();
        $sql = $this->sql->createTable($table, $this->collation());
        $this->schema->run($sql);
    }

    public function deleteTable()
    {
        $table = $this->prefix().$this->getTableName();
        $this->dropTable($table);
    }

    private function collation()
    {
        return $this->schema->getCollation();
    }

    private function prefix()
    {
        return $this->schema->getPrefix();
    }

    private function dropTable($table_name)
    {
        $sql = $this->sql->dropTable($table_name);
        $this->schema->run($sql);
    }

    
}