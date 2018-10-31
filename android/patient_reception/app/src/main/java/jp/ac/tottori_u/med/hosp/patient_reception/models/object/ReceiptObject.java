package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import android.util.Log;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by nishimura on 2018/10/16.
 */

public class ReceiptObject extends AbstractObject {

    private static final String CREATED_AT = "created_at";
    private static final String REGISTED_AT = "registed_at";
    private static final String CHECK_FLAG = "check_flag";

    private String deviceToken;
    private String uniqueId;
    private String platform;

    public boolean isCheckFlag() {
        return checkFlag;
    }

    public void setCheckFlag(boolean checkFlag) {
        this.checkFlag = checkFlag;
    }

    // レスポンス
    private boolean checkFlag;
    private String createdAt;
    private String registedAt;

    public String getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(String createdAt) {
        this.createdAt = createdAt;
    }

    public String getRegistedAt() {
        return registedAt;
    }

    public void setRegistedAt(String registedAt) {
        this.registedAt = registedAt;
    }

    public String getPlatform() {
        return platform;
    }

    public void setPlatform(String platform) {
        this.platform = platform;
    }

    private static String TAG = "RECEIPT_OBJECT";
    public String getUniqueId() {
        return uniqueId;
    }

    public void setUniqueId(String uniqueId) {
        this.uniqueId = uniqueId;
    }

    public String getDeviceToken() {
        return deviceToken;
    }

    public void setDeviceToken(String deviceToken) {
        this.deviceToken = deviceToken;
    }



    @Override
    protected void setParameters(JSONObject json) throws JSONException {
        Log.d(TAG, "josn");

        //
        this.createdAt = json.isNull(CREATED_AT)?"":json.getString(CREATED_AT);


        this.registedAt = json.isNull(REGISTED_AT)?"":json.getString(REGISTED_AT);


        this.checkFlag = json.isNull(CHECK_FLAG)?false:json.getBoolean(CHECK_FLAG);


    }
}
