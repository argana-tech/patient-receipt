<?php
namespace Fuel\Migrations;

class Alter_table_notifications_body_column_length
{
    public function up()
    {
        \DBUtil::modify_fields('notifications', array(
            'body' => array(
                'type' => 'varchar',
                'constraint' => 1024,
            ),
        ));
        \DBUtil::add_fields('notifications', array(
            'original_call_id' => array(
                'type' => 'int',
                'default' => 0,
                'after' => 'unique_id',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::modify_fields('notifications', array(
            'body' => array(
                'type' => 'varchar',
                'constraint' => 1000,
            ),
        ));
        \DBUtil::drop_fields('notifications', array(
            'original_call_id',
        ));
    }
}
