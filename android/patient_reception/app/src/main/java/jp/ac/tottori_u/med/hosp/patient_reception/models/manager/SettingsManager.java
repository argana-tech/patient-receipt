package jp.ac.tottori_u.med.hosp.patient_reception.models.manager;

import android.net.Uri;

import org.json.JSONException;
import org.json.JSONObject;

import jp.ac.tottori_u.med.hosp.patient_reception.constants.Server;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.AbstractObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.SettingsObject;
import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;

/**
 * Created by nishimura on 2018/10/04.
 */

public class SettingsManager extends AbstractManager {



    public void getProperty(ManagerCallBack<SettingsObject> callback) {

        super.setCallback(callback);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.SETTINGS_PROPERTY.getString());

//        RequestBody requestBody = new FormBody.Builder()
//                .add(REQ_DEVICE_TOKEN, token.getToken())
//                .add(REQ_PLATFORM, REQ_PLATFORM_VA_OS)
//                .build();

        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).get().build();
        client.newCall(request).enqueue(this);
    }


//    @Override
//    protected AbstractObject result() {
//        SettingsObject object = new SettingsObject();
//
//        try {
//            JSONObject jsonObject = getJsonObject();
//
//            // ビーコン
//            int beaconSendSpan = jsonObject.getInt("beacon_send_span");
//            object.setBeaconSendSpan(beaconSendSpan);
//
//
//        } catch (JSONException ex) {
//
//        }
//
//        return object;
//    }

    @Override
    protected AbstractObject createObjcet() {
        return new SettingsObject();
    }
}
