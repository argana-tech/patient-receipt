<?php
namespace Fuel\Migrations;

class Alter_table_notifications_add_column
{
    public function up()
    {
        \DBUtil::add_fields('notifications', array(
            'status' => array(
                'constraint' => 3,
                'type' => 'int',
                'unsigned' => true,
                'default' => 0,
                'after' => 'send_status',
            ),
            'filename' => array(
                'type' => 'varchar',
                'constraint' => 1000,
                'after' => 'status',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('notifications', array(
            'status',
            'filename',
        ));
    }
}
