package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import org.json.JSONException;
import org.json.JSONObject;

/**
 * Created by nishimura on 2018/10/16.
 */

public class NotificationObject extends AbstractObject {

    public String getUniqueId() {
        return uniqueId;
    }

    public void setUniqueId(String uniqueId) {
        this.uniqueId = uniqueId;
    }

    private String uniqueId;

    @Override
    protected void setParameters(JSONObject json) throws JSONException {

    }
}
