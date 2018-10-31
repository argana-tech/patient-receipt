<?php
namespace Fuel\Migrations;

class Create_Receipts_table
{
    public function up()
    {
        \DBUtil::create_table('receipts', array(
            'id' => array(
                'constraint' => 11,
                'type' => 'int',
                'auto_increment' => true,
                'unsigned' => true,
            ),
            'unique_id' => array(
                'type' => 'varchar',
                'constraint' => 14,
            ),
            'status' => array(
                'type' => 'int',
                'constraint' => 5,
            ),
            'date' => array(
                'type' => 'date',
            ),
            'created_at' => array(
                'type' => 'datetime',
            ),
            'updated_at' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'deleted_at' => array(
                'type' => 'datetime',
                'null' => true,
            ),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('receipts');
    }
}
