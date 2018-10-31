package jp.ac.tottori_u.med.hosp.patient_reception.models.manager;

import android.net.Uri;

import jp.ac.tottori_u.med.hosp.patient_reception.constants.Server;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.AbstractObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.NotificationObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.ReceiptObject;
import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;

/**
 * Created by nishimura on 2018/10/16.
 */

public class NotificationManager extends AbstractManager {


    public void postRead (NotificationObject notificationObject, ManagerCallBack callBack) {
        super.setCallback(callBack);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.NOTIFICATION_READ.getString());

        RequestBody requestBody = new FormBody.Builder()
                .add(REQ_UNIQUE_ID, notificationObject.getUniqueId())
                .build();

        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).post(requestBody).build();
        client.newCall(request).enqueue(this);
    }
    @Override
    protected AbstractObject createObjcet() {
        return new NotificationObject();
    }
}
