<?php
namespace Fuel\Migrations;

class Alter_table_notifications_add_column_use_read
{
    public function up()
    {
        \DBUtil::add_fields('notifications', array(
            'use_read' => array(
                'type' => 'bool',
                'default' => true,
                'after' => 'is_read',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('notifications', array(
            'use_read',
        ));
    }
}
