package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.lang.reflect.Array;
import java.util.ArrayList;

/**
 * Created by nishimura on 2018/10/04.
 */

public class SettingsObject extends AbstractObject {

    public static String BEACON_SEND_SPAN = "beacon_send_span";
    public static String BEACON_COLLECT_SPAN = "beacon_collect_span";
    public static String GEO_SEND_SPAN = "geo_send_span";

    public static String ACTIVE_SEND_TIME = "active_send_time";
    public static String ACTIVE_SEND_TIME_START = "start";
    public static String ACTIVE_SEND_TIME_END = "end";

    public static String ACTIVE_RECEIPT_TIME = "active_receipt_time";
    public static String ACTIVE_RECEIPT_TIME_START = "start";
    public static String ACTIVE_RECEIPT_TIME_END = "end";

    public static String BEACON_UUID_LIST = "beacon_uuid_list";

    public static String MENU_CHECK_SPAN = "menu_check_span";

    // アプリサーバー間の送信間隔（秒）
    private int beaconSendSpan;

    // ビーコンアプリ間の収集間隔（秒）
    private int  beaconCollectSpan;

    // アプリサーバー間の送信間隔（秒）
    private int  geoSendSpan;

    // 位置情報送信可能時間帯
    private ActiveSendTime activeSendTime = new ActiveSendTime();



    //  受付可能時間帯
    private ActiveReceiptTime activeReceiptTime = new ActiveReceiptTime();

    // UUIDリスト
    private ArrayList<String> beaconUuidList = new ArrayList<String>();

    // 受付可否API呼び出し間隔
    private int menuCheckSpan;

    public int getBeaconSendSpan() {
        return beaconSendSpan;
    }

    public void setBeaconSendSpan(int beaconSendSpan) {
        this.beaconSendSpan = beaconSendSpan;
    }

    public int getBeaconCollectSpan() {
        return beaconCollectSpan;
    }

    public void setBeaconCollectSpan(int beaconCollectSpan) {
        this.beaconCollectSpan = beaconCollectSpan;
    }

    public int getGeoSendSpan() {
        return geoSendSpan;
    }

    public void setGeoSendSpan(int geoSendSpan) {
        this.geoSendSpan = geoSendSpan;
    }

    public ActiveSendTime getActiveSendTime() {
        return activeSendTime;
    }

    public void setActiveSendTime(ActiveSendTime activeSendTime) {
        this.activeSendTime = activeSendTime;
    }

    public ActiveReceiptTime getActiveReceiptTime() {
        return activeReceiptTime;
    }

    public void setActiveReceiptTime(ActiveReceiptTime activeReceiptTime) {
        this.activeReceiptTime = activeReceiptTime;
    }

    public ArrayList<String> getBeaconUuidList() {
        return beaconUuidList;
    }

    public void setBeaconUuidList(ArrayList<String> beaconUuidList) {
        this.beaconUuidList = beaconUuidList;
    }

    public int getMenuCheckSpan() {
        return menuCheckSpan;
    }

    public void setMenuCheckSpan(int menuCheckSpan) {
        this.menuCheckSpan = menuCheckSpan;
    }


    @Override
    public void setParameters(JSONObject jsonObject) throws JSONException {

        // ビーコン送信間隔
        int beaconSendSpan = jsonObject.isNull(BEACON_SEND_SPAN)?-1:jsonObject.getInt(BEACON_SEND_SPAN);
        this.setBeaconSendSpan(beaconSendSpan);

        // ビーコン収集時間
        int beaconCollectSpan = jsonObject.isNull(BEACON_COLLECT_SPAN)?-1:jsonObject.getInt(BEACON_COLLECT_SPAN);
        this.setBeaconCollectSpan(beaconCollectSpan);

        // 位置情報送信間隔
        int geoSendSpan = jsonObject.isNull(GEO_SEND_SPAN)?-1:jsonObject.getInt(GEO_SEND_SPAN);
        this.setGeoSendSpan(geoSendSpan);

        // 送信可能時間帯
        JSONObject jsonActiveSendTime = jsonObject.isNull(ACTIVE_SEND_TIME)?null:jsonObject.getJSONObject(ACTIVE_SEND_TIME);
        if (jsonActiveSendTime != null) {
            // 開始
            String start = jsonActiveSendTime.isNull(ACTIVE_SEND_TIME_START)?null:jsonActiveSendTime.getString(ACTIVE_SEND_TIME_START);
            this.activeSendTime.setStart(start);

            // 終了
            String end = jsonActiveSendTime.isNull(ACTIVE_SEND_TIME_END)?null:jsonActiveSendTime.getString(ACTIVE_SEND_TIME_END);
            this.activeSendTime.setEnd(end);
        }


        // 受付可能時間帯
        JSONObject jsonActiveReceiptTime = jsonObject.isNull(ACTIVE_RECEIPT_TIME)?null:jsonObject.getJSONObject(ACTIVE_RECEIPT_TIME);
        if (jsonActiveReceiptTime != null) {
            // 開始
            String start = jsonActiveReceiptTime.isNull(ACTIVE_RECEIPT_TIME_START)?null:jsonActiveReceiptTime.getString(ACTIVE_RECEIPT_TIME_START);
            this.activeReceiptTime.setStart(start);

            // 終了
            String end = jsonActiveReceiptTime.isNull(ACTIVE_RECEIPT_TIME_END)?null:jsonActiveReceiptTime.getString(ACTIVE_RECEIPT_TIME_END);
            this.activeReceiptTime.setEnd(end);
        }

        // uuid
        JSONArray beaconUuidArray = jsonObject.getJSONArray(BEACON_UUID_LIST);
        if (beaconUuidArray != null) {
            for (int i = 0; i < beaconUuidArray.length(); i++) {
                String uuid = (String)beaconUuidArray.get(i);
                this.beaconUuidList.add(uuid);

            }
        }

        // 受付可否API呼び出し間隔
        int menuCheckSpan = jsonObject.isNull(MENU_CHECK_SPAN)?-1:jsonObject.getInt(MENU_CHECK_SPAN);
        this.setMenuCheckSpan(menuCheckSpan);

    }


    public class ActiveSendTime {


        private String start;
        private String end;

        public String getStart() {
            return start;
        }

        public void setStart(String start) {
            this.start = start;
        }

        public String getEnd() {
            return end;
        }

        public void setEnd(String end) {
            this.end = end;
        }

        public boolean isPossible() {

            return true;
        }
    }

    public class ActiveReceiptTime {
        private String start;
        private String end;

        public String getStart() {
            return start;
        }

        public void setStart(String start) {
            this.start = start;
        }

        public String getEnd() {
            return end;
        }

        public void setEnd(String end) {
            this.end = end;
        }

        public boolean isPossible() {
            return true;
        }
    }


}
