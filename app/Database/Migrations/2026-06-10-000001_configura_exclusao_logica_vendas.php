<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfiguraExclusaoLogicaVendas extends Migration
{
    public function up()
    {
        $this->db->query(
            'ALTER TABLE `vendas` MODIFY `deleted_at` DATETIME NULL DEFAULT NULL'
        );
        $this->db->query(
            "UPDATE `vendas` SET `deleted_at` = NULL WHERE `deleted_at` = '0000-00-00 00:00:00'"
        );
    }

    public function down()
    {
        $this->db->query(
            "UPDATE `vendas` SET `deleted_at` = '0000-00-00 00:00:00'"
        );
        $this->db->query(
            'ALTER TABLE `vendas` MODIFY `deleted_at` DATETIME NOT NULL'
        );
    }
}
