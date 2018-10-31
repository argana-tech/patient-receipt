<?php
class Controller_Api extends Controller_Abstract
{
    protected $format = 'json';

    /*
     * 患者IDから患者情報を取得
     */
    public function action_get_patient_detail($patient_id)
    {
        $unique_id = Uniqueid::create();

        // 電子カルテ連携
        $response = Register::getPatient($unique_id, $patient_id);

        /*test
        $response = array(
            'is_success' => 1,
            'unique_id' => $unique_id,
            'name' => '鳥取太郎',
            'birth_at' => '1940年1月1日',
        );*/

        return $this->response($response);
    }

    /*
     * ユニークID生成
     */
    public function create($id)
    {
        \Package::load("qrcode");
        $qr = QRCode::png($id, false, QR_ECLEVEL_L, 10, 1);
        exit;
    }

    /*
     * QRコード画像表示
     */
    public function action_qrimage($id)
    {
        \Package::load("qrcode");
        $qr = QRCode::png($id, false, QR_ECLEVEL_L, 10, 1);
        exit;
    }
}
