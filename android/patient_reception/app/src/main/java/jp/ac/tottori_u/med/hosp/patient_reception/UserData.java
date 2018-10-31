package jp.ac.tottori_u.med.hosp.patient_reception;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;

/**
 * Created by nishimura on 2018/10/15.
 */

public class UserData {

    private static final String PATIENT_RECEPTION_DATA = "patientReceptionData";
    private static final String UNIQUE_ID = "uniqueId";

    /**
     *
     * @param activity
     * @return
     */
    public static String getUniqueId (Activity activity) {
        SharedPreferences prefer = activity.getSharedPreferences(PATIENT_RECEPTION_DATA, Context.MODE_PRIVATE);

        String uniqueId = prefer.getString(UNIQUE_ID, "");
        return uniqueId;
    }

    /**
     *
     * @param activity
     * @param uniqueId
     */
    public static void setUniqueId (Activity activity, String uniqueId) {
        SharedPreferences prefer = activity.getSharedPreferences(PATIENT_RECEPTION_DATA, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = prefer.edit();
        editor.putString(UNIQUE_ID, uniqueId);
        editor.commit();
    }
}
