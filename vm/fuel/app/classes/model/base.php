<?php
class Model_Base extends \Orm\Model_Soft
{
    protected static $_soft_delete = array(
        'deleted_field' => 'deleted_at',
        'mysql_timestamp' => true,
    );
}
