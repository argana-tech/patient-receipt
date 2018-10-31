package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by nishimura on 2018/10/03.
 */

public abstract class AbstractObject {
    private String message;
    private String error;
    private boolean result;

    // レスポンスキー
    private static final String RES_RESULT = "result";
    private static final String RES_UNIQUE_ID = "unique_id";
    private static final String RES_MESSAGE = "message";
    private static final String RES_ERROR = "error";

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getError() {
        return error;
    }

    public void setError(String error) {
        this.error = error;
    }

    public boolean isResult() {
        return result;
    }

    public void setResult(boolean result) {
        this.result = result;
    }

    public void setCommonParameters(JSONObject jsonObject) throws JSONException {

        // 結果
        this.result = jsonObject.isNull(RES_RESULT)?false:jsonObject.getBoolean(RES_RESULT);

        // メッセージ
        this.message = jsonObject.isNull(RES_MESSAGE)?"":jsonObject.getString(RES_MESSAGE);

        // エラー
        this.error = jsonObject.isNull(RES_ERROR)?"":jsonObject.getString(RES_ERROR);

        this.setParameters(jsonObject);

    }

    abstract protected void setParameters(JSONObject json) throws JSONException;
}
