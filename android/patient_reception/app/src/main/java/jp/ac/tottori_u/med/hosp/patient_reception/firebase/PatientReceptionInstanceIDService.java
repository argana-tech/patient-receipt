package jp.ac.tottori_u.med.hosp.patient_reception.firebase;

import android.content.Intent;
import android.util.Log;

import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.FirebaseInstanceIdService;

import jp.ac.tottori_u.med.hosp.patient_reception.constants.IntentName;

/**
 * Created by nishimura on 2018/09/21.
 */

public class PatientReceptionInstanceIDService extends FirebaseInstanceIdService {
    private static String TAG = "PatientReceptionInstanceIDService";
    @Override
    public void onTokenRefresh() {
        super.onTokenRefresh();

        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
        Log.d(TAG, "Refreshed token: " + refreshedToken);

        sendRegistrationToServer(refreshedToken);
    }

    private void sendRegistrationToServer(String token) {
        Log.d(TAG, "token:" + token);
        Intent intent = new Intent(IntentName.REFRESH_TOKEN);
        sendBroadcast(intent);
    }
}
