package jp.ac.tottori_u.med.hosp.patient_reception.activity;

import android.app.Activity;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.messaging.FirebaseMessaging;
import com.google.zxing.integration.android.IntentIntegrator;
import com.google.zxing.integration.android.IntentResult;

import jp.ac.tottori_u.med.hosp.patient_reception.R;
import jp.ac.tottori_u.med.hosp.patient_reception.UserData;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.AbstractManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.ReceiptManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.CheckObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.ReceiptObject;

public class RegisterActivity extends CommonActivity {

    private static final String MESSAGE = "QRコードを読み取りました。\n登録してもよろしければ、OKボタンをクリックしてください。";
    private static final String TAG = "RegisterActivity";

    private String uniqueId = "";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

//        new IntentIntegrator(RegisterActivity.this).initiateScan();
        IntentIntegrator integrator = new IntentIntegrator(this);
        integrator.setDesiredBarcodeFormats(IntentIntegrator.QR_CODE);
        integrator.setPrompt("");
        integrator.setOrientationLocked(false);
        integrator.setCameraId(0);  // Use a specific camera of the device
        integrator.setBeepEnabled(false);
        integrator.setBarcodeImageEnabled(true);
        integrator.initiateScan();

        TextView textView = (TextView) findViewById(R.id.textView);
        textView.setText(MESSAGE);

        Button button = (Button) findViewById(R.id.registerButton);
        button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                register();
            }
        });

    }

    private void register () {
        ReceiptObject receiptObject = new ReceiptObject();
        receiptObject.setUniqueId(uniqueId);
        String token = FirebaseInstanceId.getInstance().getToken();
        receiptObject.setDeviceToken(token);
        receiptObject.setPlatform("android");
        new ReceiptManager().postRegister(receiptObject, new ManagerCallBack<ReceiptObject>() {

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
    private void alert(final ReceiptObject resultObject) {
        if (resultObject.isResult()) {
            Log.d(TAG, "成功");
            UserData.setUniqueId(this, uniqueId);

        } else {
            Log.d(TAG, "成功");
        }


        new AlertDialog.Builder(this)
                .setTitle("登録")
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

    /**
     *
     * @param resultObject
     */
    private void callMainActivity(ReceiptObject resultObject) {
        Intent intent = new Intent();
//        intent.putExtra(MainActivity.RESULT_KEY_FROM, MainActivity.RESULT_FROM_REGISTER);
        intent.putExtra(MainActivity.RESULT_KEY_RESULT, resultObject.isResult());
        String message = resultObject.getMessage();//resultObject.getCreatedAt();
        if (resultObject.getCreatedAt() != null && !resultObject.getCreatedAt().isEmpty()) {
            message = message + "\n" + resultObject.getCreatedAt();
        }
        intent.putExtra(MainActivity.RESULT_KEY_MESSAGE, message);
        setResult(RESULT_OK, intent);

    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        IntentResult result = IntentIntegrator.parseActivityResult(requestCode, resultCode, data);
        if(result != null) {
            String qrCode = result.getContents();
            if (qrCode != null) {
                uniqueId = qrCode;
                Log.d("readQR", result.getContents());
            } else {
                this.finish();
            }
        } else {
            // 閉じる
            super.onActivityResult(requestCode, resultCode, data);
        }
    }

}
