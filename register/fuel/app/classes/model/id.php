<?php

class Model_Id extends \Orm\Model
{
    protected static $_table_name = 'ids';

    protected static $_properties = array(
        'id',
        'uniqueid',
        'date_at',
    );
}
