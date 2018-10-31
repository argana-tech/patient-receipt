<?php
class Controller_Root extends Controller_Abstract
{
    public function action_index()
    {
// test
// $patient = Register::getPatient('20180404101012', '12345678');
        $presenter = Presenter::forge('root/index');

        $this->template->set('title', 'トップ');
        $this->template->set('content', $presenter);
    }

    public function action_detail()
    {
        $patient_id = Input::post('patient_id');
        $patient_id = mb_convert_kana($patient_id, 'n');	// 全角を半角に修正

        $presenter = Presenter::forge('root/detail');
        $presenter->set('patient_id', $patient_id);

        $this->template->set('title', '確認');
        $this->template->set('content', $presenter);
    }

    public function action_qr()
    {
        $unique_id = Input::post('unique_id');

        $presenter = Presenter::forge('root/qr');
        $presenter->set('unique_id', $unique_id);

        $this->template->set('title', 'QRコード');
        $this->template->set('content', $presenter);
    }
}
