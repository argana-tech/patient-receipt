package jp.ac.tottori_u.med.hosp.patient_reception.models.manager;

/**
 * Created by nishimura on 2018/10/03.
 */

import android.net.Uri;

import java.io.IOException;

import jp.ac.tottori_u.med.hosp.patient_reception.constants.Server;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.AbstractObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.CheckObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.SettingsObject;
import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public class CheckManager extends AbstractManager {



    public void postRegister(CheckObject checkObject, ManagerCallBack<CheckObject> callBack) {

        super.setCallback(callBack);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.CHECK_REGISTER.getString());

        RequestBody requestBody = new FormBody.Builder()
                .add(REQ_UNIQUE_ID, checkObject.getUniqueId())
                .build();

        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).post(requestBody).build();
        client.newCall(request).enqueue(this);
    }

    public void postRecipet(CheckObject checkObject, ManagerCallBack<CheckObject> callBack) {

        super.setCallback(callBack);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.CHECK_TIME.getString());

        RequestBody requestBody = new FormBody.Builder()
                .add(REQ_UNIQUE_ID, checkObject.getUniqueId())
                .build();

        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).post(requestBody).build();
        client.newCall(request).enqueue(this);
    }


    @Override
    protected AbstractObject createObjcet() {
        return new CheckObject();
    }
}
