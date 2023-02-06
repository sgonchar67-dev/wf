<?php

namespace App\Migrations;

trait AddMultipleSqlTrait
{
    private function addMultipleSql(string $multipleSql)
    {
        foreach (explode(';', trim($multipleSql)) as $sql) {
            if (trim($sql)) {
                $this->addSql($sql);
            }
        }
    }
}
