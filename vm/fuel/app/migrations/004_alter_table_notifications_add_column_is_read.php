<?php
namespace Fuel\Migrations;

class Alter_table_notifications_add_column_is_read
{
    public function up()
    {
        \DBUtil::add_fields('notifications', array(
            'is_read' => array(
                'type' => 'bool',
                'default' => false,
                'after' => 'try_numbers',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('notifications', array(
            'is_read',
        ));
    }
}
