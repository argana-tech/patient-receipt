<?php
namespace Fuel\Migrations;

class Create_notifications_table
{
    public function up()
    {
        \DBUtil::create_table('notifications', array(
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
            'body' => array(
                'type' => 'varchar',
                'constraint' => 1000,
            ),
            'send_at' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'send_status' => array(
                'type' => 'bool',
                'default' => false,
            ),
            'try_numbers' => array(
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
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
        \DBUtil::drop_table('notifications');
    }
}
