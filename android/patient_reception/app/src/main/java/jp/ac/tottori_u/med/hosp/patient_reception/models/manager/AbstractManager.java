package jp.ac.tottori_u.med.hosp.patient_reception.models.manager;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

import org.json.JSONException;
import org.json.JSONObject;

import java.io.IOException;
import java.net.ConnectException;

import jp.ac.tottori_u.med.hosp.patient_reception.MyApplication;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.AbstractObject;
import okhttp3.Call;
import okhttp3.Callback;
import okhttp3.Response;

/**
 * Created by nishimura on 2018/10/03.
 */

public abstract class AbstractManager<T> implements Callback {

    // リクエストキー
    protected static final String REQ_DEVICE_TOKEN = "device_token";
    protected static final String REQ_PLATFORM= "platform";

    protected static final String REQ_UNIQUE_ID = "unique_id";

    // リクエスト値
    protected static final String REQ_PLATFORM_VA_OS = "android";

    private ManagerCallBack callback;

    // エラーメッセージ
    private static final String ERROR_MSG_NETWORK = "インターネットに接続されていません";

    public void setCallback(ManagerCallBack callback) {
        this.callback = callback;
    }



    @Override
    public void onFailure(Call call, IOException e) {

        final AbstractObject resultObject = createObjcet();
        resultObject.setResult(false);

        if (e.getClass() == ConnectException.class) {
            // ネットワークの接続エラー
            resultObject.setMessage(ERROR_MSG_NETWORK);
        } else {
            resultObject.setMessage(e.getLocalizedMessage());
        }

        new Thread(new Runnable() {
            @Override
            public void run() {
                callback.callback(resultObject);
            }
        }).start();
    }

    @Override
    public void onResponse(Call call, Response response) throws IOException {
        final String resString = response.body().string();
        final AbstractObject resultObject = createObjcet();
        JSONObject jsonObject = null;
        try {
            jsonObject = new JSONObject(resString);
            resultObject.setCommonParameters(jsonObject);
        } catch (JSONException e) {
            resultObject.setResult(false);
            resultObject.setMessage("通信エラー");
            e.printStackTrace();
        }

        // 別スレッドで実行
        new Thread(new Runnable() {
            @Override
            public void run() {
                callback.callback(resultObject);
            }
        }).start();
    }


//    abstract protected AbstractObject result();
    abstract protected AbstractObject createObjcet();


}
