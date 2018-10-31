<?php
namespace Fuel\Migrations;

class Alter_table_receipts_add_column_receipt_number
{
    public function up()
    {
        \DBUtil::add_fields('receipts', array(
            'receipt_number' => array(
                'type' => 'varchar',
                'constraint' => 20,
                'default' => null,
                'null' => true,
                'after' => 'date',
            ),
            'message' => array(
                'type' => 'varchar',
                'constraint' => 1024,
                'default' => null,
                'null' => true,
                'after' => 'receipt_number',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('receipts', array(
            'message',
            'receipt_number',
        ));
    }
}
