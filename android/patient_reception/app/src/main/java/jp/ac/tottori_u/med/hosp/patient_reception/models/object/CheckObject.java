package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by nishimura on 2018/10/03.
 */

public class CheckObject extends AbstractObject {

    public static String CHECK_FLAG = "check_flag";
    public static String BUTTON = "button";

    private String uniqueId;
    private boolean checkFlg;
    private String button;

    public String getButton() {
        return button;
    }

    public void setButton(String button) {
        this.button = button;
    }

    public String getUniqueId() {
        return uniqueId;
    }

    public void setUniqueId(String uniqueId) {
        this.uniqueId = uniqueId;
    }

    public Boolean isCheckFlg() {
        return checkFlg;
    }

    public void setCheckFlg(Boolean checkFlg) {
        this.checkFlg = checkFlg;
    }

    @Override
    protected void setParameters(JSONObject json) throws JSONException {
        // CheckFlg
        this.checkFlg = json.isNull(CHECK_FLAG)?false:json.getBoolean(CHECK_FLAG);

        this.button = json.isNull(BUTTON)?"登録":json.getString(BUTTON);
    }
}
