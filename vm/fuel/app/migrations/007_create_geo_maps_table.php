<?php
namespace Fuel\Migrations;

class Create_Geo_maps_table
{
    public function up()
    {
        \DBUtil::create_table('geo_maps', array(
            'id' => array(
                'constraint' => 11,
                'type' => 'int',
                'auto_increment' => true,
                'unsigned' => true,
            ),
            'pref_code' => array(
                'type' => 'int',
                'constraint' => 10,
            ),
            'pref' => array(
                'type' => 'varchar',
                'constraint' => 50,
                'default' => '',
            ),
            'city' => array(
                'type' => 'varchar',
                'constraint' => 100,
                'default' => '',
            ),
            'address' => array(
                'type' => 'varchar',
                'constraint' => 100,
                'default' => '',
            ),
            'lat' => array(
                'type' => 'double',
                'constraint' => '8,6',
            ),
            'lon' => array(
                'type' => 'double',
                'constraint' => '9,6',
            ),
            'geo_hash' => array(
                'type' => 'varchar',
                'constraint' => 50,
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
        \DBUtil::drop_table('geo_maps');
    }
}
