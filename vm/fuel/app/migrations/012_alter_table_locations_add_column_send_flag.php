<?php
namespace Fuel\Migrations;

class Alter_table_locations_add_column_send_flag
{
    public function up()
    {
        \DBUtil::add_fields('geo_locations', array(
            'send_flag' => array(
                'type' => 'bool',
                'default' => false,
                'after' => 'time',
            ),
        ));
        \DBUtil::add_fields('beacon_locations', array(
            'send_flag' => array(
                'type' => 'bool',
                'default' => false,
                'after' => 'time',
            ),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('beacon_locations', array(
            'send_flag',
        ));
        \DBUtil::drop_fields('geo_locations', array(
            'send_flag',
        ));
    }
}
