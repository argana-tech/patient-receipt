package jp.ac.tottori_u.med.hosp.patient_reception.activity;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.view.Window;
import android.view.WindowManager;

/**
 * Created by nishimura on 2018/09/21.
 */

public class PopUpActivity extends AppCompatActivity {

    @Override
    public void onAttachedToWindow() {

        super.onAttachedToWindow();
        Window window = getWindow();
        window.addFlags(
                WindowManager.LayoutParams.FLAG_TURN_SCREEN_ON
                        | WindowManager.LayoutParams.FLAG_SHOW_WHEN_LOCKED
                        | WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON
                        | WindowManager.LayoutParams.FLAG_DISMISS_KEYGUARD
        );
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Intent intent = getIntent();
        String title = "";
        String text = intent.getStringExtra("text");

        openDialog(text);

    }


    /**
     * ダイアログを表示する
     * @param text
     */
    void openDialog(String text) {
        // ダイアログを表示する
        AlertDialog.Builder alertBuilder = new AlertDialog.Builder(PopUpActivity.this);
        alertBuilder.setCancelable(false);
        alertBuilder.setTitle("お呼び出し");
        alertBuilder.setMessage(text);

        alertBuilder.setNegativeButton("OK", new DialogInterface.OnClickListener() {

            @Override
            public void onClick(DialogInterface dialog, int which) {
                dialog.dismiss();

                finish();
            }
        });
        alertBuilder.create().show();
    }
}
