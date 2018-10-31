<?php
namespace Fuel\Migrations;

class Alter_table_terminals_add_column_application_type
{
    public function up()
    {
        \DBUtil::add_fields('terminals', array(
            'application_type' => array(
                'type' => 'int',
                'constraint' => 3,
                'default' => 0,
                'after' => 'platform',
            ),
        ));
        \DBUtil::add_fields('notifications', array(
            'application_type' => array(
                'type' => 'int',
                'constraint' => 3,
                'default' => 0,
                'after' => 'platform',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('notifications', array(
            'application_type',
        ));
        \DBUtil::drop_fields('terminals', array(
            'application_type',
        ));
    }
}
