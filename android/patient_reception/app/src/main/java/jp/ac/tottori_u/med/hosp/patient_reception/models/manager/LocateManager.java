package jp.ac.tottori_u.med.hosp.patient_reception.models.manager;

import android.net.Uri;

import java.util.ArrayList;
import java.util.List;

import jp.ac.tottori_u.med.hosp.patient_reception.constants.Server;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.AbstractObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.CheckObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.LocateObject;
import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;

/**
 * Created by nishimura on 2018/10/16.
 */

public class LocateManager extends AbstractManager {

    public void postBeacon(LocateObject locateObject, ManagerCallBack<LocateObject> callBack) {

        super.setCallback(callBack);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.LOCATE_BEACON.getString());

        FormBody.Builder builder = new FormBody.Builder();
        builder.add(REQ_UNIQUE_ID, locateObject.getUniqueId());

        List<LocateObject.Beacon> beacons = locateObject.getBeacons();
        for (int i = 0; i < beacons.size();i++) {
            LocateObject.Beacon beacon = beacons.get(i);
            builder.add(beacon.getNameKey(i), beacon.getName());
            builder.add(beacon.getLevelKey(i), String.valueOf(beacon.getLevel()));
            builder.add(beacon.getTimeKey(i), beacon.getTime());
        }


        RequestBody requestBody = builder.build();
        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).post(requestBody).build();
        client.newCall(request).enqueue(this);
    }

    public void postGeo(LocateObject locateObject, ManagerCallBack<LocateObject> callBack) {

        super.setCallback(callBack);

        Uri.Builder uriBuilder = new Uri.Builder();
        uriBuilder.scheme("http");
        uriBuilder.authority(Server.HOST);
        uriBuilder.path(Server.API.LOCATE_GEO.getString());

        FormBody.Builder builder = new FormBody.Builder();
        builder.add(REQ_UNIQUE_ID, locateObject.getUniqueId());

//        List<LocateObject.Geo> geos = locateObject.getGeos();
//        for (int i = 0; i < geos.size();i++) {
//            LocateObject.Geo geo = geos.get(i);
//
//            builder.add(geo.getLatitudeKey(i), String.valueOf(geo.getLatitude()));
//            builder.add(geo.getLongitudeKey(i), String.valueOf(geo.getLongitude()));
//        }

        LocateObject.Geo geo = locateObject.getGeo();
        builder.add(geo.getLatitudeKey(), String.valueOf(geo.getLatitude()));
        builder.add(geo.getLongitudeKey(), String.valueOf(geo.getLongitude()));

        RequestBody requestBody = builder.build();
        OkHttpClient client = new OkHttpClient();

        Request request = new Request.Builder().url(uriBuilder.toString()).post(requestBody).build();
        client.newCall(request).enqueue(this);
    }

    @Override
    protected AbstractObject createObjcet() {
        return new LocateObject();
    }


}
