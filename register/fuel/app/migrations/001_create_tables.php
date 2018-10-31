<?php
namespace Fuel\Migrations;

class Create_tables
{
    public function up()
    {
        \DBUtil::create_table('ids', array(
            'id' => array(
                'type' => 'int',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true,
            ),
            'uniqueid' => array(
                'type' => 'int',
                'constraint' => 6,
            ),
            'date_at' => array(
                'type' => 'int',
                'constraint' => 8,
            ),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('ids');
    }
}
