<?php
class Uniqueid
{
    const PRE_ID = 1;		// 個人スマホは1、貸出スマホは0
    const ID_LENGTH = 5;

    public static function create()
    {
//test
//return 20180404101012;

        $date = date("Ymd");

        $db = Database_Connection::instance();
        $db->start_transaction();

        $last_uniqueid = 1;

        try {
            // id取得、更新
            $id_model = Model_Id::find('first', array(
                'where' => array(
                    array('date_at', '=', $date),
                ),
                'order_by' => array(
                    'date_at' => 'desc',
                    'uniqueid' => 'desc',
                ),
            ));

            if ($id_model) {
                $id_model->uniqueid += 1;
                $id_model->save();

                $last_uniqueid = $id_model->uniqueid;
            } else {
                $id_model = new Model_Id(
                    array(
                        'uniqueid' => $last_uniqueid,
                        'date_at' => $date,
                    )
                );
                $id_model->save();
            }

            $db->commit_transaction();
        } catch (Exception $e) {
            $db->rollback_transaction();
        }

        $uid = $date . self::PRE_ID . sprintf(
            '%0' . self::ID_LENGTH . 'd', 
            $last_uniqueid
        );

        return $uid;
    }
}