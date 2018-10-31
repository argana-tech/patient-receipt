<?php
namespace Fuel\Migrations;

class Create_locations_table
{
    public function up()
    {
        \DBUtil::create_table('geo_locations', array(
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
            'address' => array(
                'type' => 'varchar',
                'constraint' => 512,
                'default' => null,
                'null' => true,
            ),
            'latitude' => array(
                'type' => 'double',
                'constraint' => '8,6',
                'default' => 0.0,
            ),
            'longitude' => array(
                'type' => 'double',
                'constraint' => '9,6',
                'default' => 0.0,
            ),
            'time' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'datetime',
            ),
        ), array('id'));

        \DBUtil::create_table('beacon_locations', array(
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
            'address' => array(
                'type' => 'varchar',
                'constraint' => 255,
                'default' => null,
                'null' => true,
            ),
            'beacon_name' => array(
                'type' => 'varchar',
                'constraint' => 255,
                'default' => null,
                'null' => true,
            ),
            'radio_strength' => array(
                'type' => 'double',
                'constraint' => '8,5',
                'default' => 0.0,
            ),
            'time' => array(
                'type' => 'datetime',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'datetime',
            ),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('geo_locations');
        \DBUtil::drop_table('beacon_locations');
    }
}
