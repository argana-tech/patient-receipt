package jp.ac.tottori_u.med.hosp.patient_reception.activity;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.Handler;
import android.os.Looper;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;

import jp.ac.tottori_u.med.hosp.patient_reception.R;
import jp.ac.tottori_u.med.hosp.patient_reception.UserData;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.ReceiptManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.ReceiptObject;

public class ReceptionActivity extends CommonActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_reception);

        receipt();
    }

    private void receipt () {
        final ReceiptObject receiptObject = new ReceiptObject();
        receiptObject.setUniqueId(UserData.getUniqueId(this));
        ReceiptManager receiptManager = new ReceiptManager();
//        final ReceptionActivity receptionActivity = this;
        receiptManager.postEntry(receiptObject, new ManagerCallBack<ReceiptObject>() {
            @Override
            public void callback(final ReceiptObject resultObject) {
                Handler mHandler = new Handler(Looper.getMainLooper());
                mHandler.post(new Runnable() {
                    @Override
                    public void run() {

                        alert(resultObject);
                    }
                });
            }
        });
    }
    /**
     *
     * @param resultObject
     */
    private void callMainActivity(ReceiptObject resultObject) {
        Intent intent = new Intent();
//        intent.putExtra(MainActivity.RESULT_KEY_FROM, MainActivity.RESULT_FROM_REGISTER);
        intent.putExtra(MainActivity.RESULT_KEY_RESULT, resultObject.isResult());
        intent.putExtra(MainActivity.RESULT_KEY_MESSAGE, resultObject.getMessage());
        intent.putExtra(MainActivity.RESULT_KEY_CHECK_FLAG, resultObject.isCheckFlag());
        setResult(RESULT_OK, intent);

    }
    private void alert(final ReceiptObject resultObject) {
        new AlertDialog.Builder(this)
                .setTitle("受付")
                .setMessage(resultObject.getMessage())
                .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        // OK button pressed
                        callMainActivity(resultObject);
                        finish();
                    }
                })
                .show();

    }

}
