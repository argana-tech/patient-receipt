<?php
namespace Fuel\Migrations;

class Create_original_calls_table
{
    public function up()
    {
        \DBUtil::create_table('original_calls', array(
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
            'receipt_number' => array(
                'type' => 'varchar',
                'constraint' => 4,
            ),
            'shinryouka_code' => array(
                'type' => 'varchar',
                'constraint' => 3,
            ),
            'shinryouka_name' => array(
                'type' => 'varchar',
                'constraint' => 20,
            ),
            'reserve_name' => array(
                'type' => 'varchar',
                'constraint' => 42,
            ),
            'reserve_date' => array(
                'type' => 'varchar',
                'constraint' => 8,
            ),
            'reserve_type' => array(
                'type' => 'varchar',
                'constraint' => 2,
            ),
            'reserve_items' => array(
                'type' => 'varchar',
                'constraint' => 3,
            ),
            'room_number' => array(
                'type' => 'varchar',
                'constraint' => 2,
            ),
            'reserve_tantou' => array(
                'type' => 'varchar',
                'constraint' => 8,
            ),
            'reserve_start_time' => array(
                'type' => 'varchar',
                'constraint' => 5,
            ),
            'hojoka' => array(
                'type' => 'varchar',
                'constraint' => 1,
            ),
            'message' => array(
                'type' => 'varchar',
                'constraint' => 1024,
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
        \DBUtil::drop_table('original_calls');
    }
}
