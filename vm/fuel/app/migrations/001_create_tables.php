<?php
namespace Fuel\Migrations;

class Create_tables
{
    public function up()
    {
        \DBUtil::create_table('terminals', array(
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
            'device_token' => array(
                'type' => 'varchar',
                'constraint' => 255,
            ),
            'platform' => array(
                'type' => 'varchar',
                'constraint' => 255,
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
        \DBUtil::drop_table('terminals');
    }
}
