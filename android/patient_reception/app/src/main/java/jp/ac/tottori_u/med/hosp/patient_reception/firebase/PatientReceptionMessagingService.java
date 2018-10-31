package jp.ac.tottori_u.med.hosp.patient_reception.firebase;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;
import android.support.v4.app.NotificationCompat;
import android.util.Log;
import android.widget.RemoteViews;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import java.util.Map;

import jp.ac.tottori_u.med.hosp.patient_reception.R;
import jp.ac.tottori_u.med.hosp.patient_reception.activity.MainActivity;
import jp.ac.tottori_u.med.hosp.patient_reception.MyApplication;
import jp.ac.tottori_u.med.hosp.patient_reception.activity.PopUpActivity;
import jp.ac.tottori_u.med.hosp.patient_reception.constants.IntentName;

/**
 * Created by nishimura on 2018/09/21.
 */

public class PatientReceptionMessagingService extends FirebaseMessagingService {

    private static String TAG = "PatientReceptionMessagingService";

    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {

        Log.d(TAG, "From: " + remoteMessage.getFrom());

        if (remoteMessage.getData().size() > 0) {
            Log.d(TAG, "Message data payload: " + remoteMessage.getData());
            Map<String, String> data = remoteMessage.getData();

            // データ取得
            String title = data.get("title");
            String text = data.get("text");

            Intent intent = new Intent(IntentName.RECIVE_MESSAGE);
            intent.putExtra("title", title);
            intent.putExtra("text", text);
            sendBroadcast(intent);

            // 通知を表示
            sendNotification(title, text);
        }
    }

    /**
     * 通知メッセージ
     * @param title
     * @param text
     */
    private void sendNotification(String title, String text) {

        Intent intent = new Intent(this, MainActivity.class);
//        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        intent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
        PendingIntent pendingIntent = PendingIntent.getActivity(
                this,
                0 /* Request code */,
                intent,
                PendingIntent.FLAG_ONE_SHOT);

        RemoteViews customView = new RemoteViews(getPackageName(), R.layout.notification_layout);
        customView.setTextViewText(R.id.textview_text, text);

        String channelId = getString(R.string.default_notification_channel_id);
        Uri defaultSoundUri= RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);

        NotificationCompat.Builder notificationBuilder =
                new NotificationCompat.Builder(this, channelId)
//                        .setSmallIcon(R.drawable.ic_stat_ic_notification)
//                        .setDefaults(Notification.DEFAULT_ALL)
//                        .setTicker("Ticker")
//                        .setWhen(System.currentTimeMillis())
                        .setSmallIcon(R.drawable.ic_launcher_background)
                        .setVibrate(new long[] {0, 1000})
//                        .setContentTitle(title)
//                        .setContentText(text)
                        .setAutoCancel(true)
                        .setSound(defaultSoundUri)
                        .setContentIntent(pendingIntent)
                        .setContent(customView)
                        .setCustomContentView(customView);
//                .setCustomBigContentView(customView);
//        notificationBuilder.setStyle(new NotificationCompat.DecoratedCustomViewStyle());
        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);


        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel channel = new NotificationChannel(
                    channelId,
                    "Channel human readable title",
                    NotificationManager.IMPORTANCE_DEFAULT);
            notificationManager.createNotificationChannel(channel);
        }

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.JELLY_BEAN) {
            notificationBuilder.setCustomBigContentView(customView);
        }

        // 通知を表示
        notificationManager.notify(0 /* ID of notification */, notificationBuilder.build());

        // 画面にかかわらずダイアログを表示
        showActivity(title, text, MyApplication.getInstance().getApplicationContext());

    }

    /**
     * 画面にかかわらずダイアログを表示
     * @param title
     * @param text
     * @param context
     */
    private void showActivity(String title, String text, Context context ){

        if (MyApplication.getInstance().isAppForeground()) {
            //
        } else {
            Intent intent = new Intent(context, PopUpActivity.class);

            intent.putExtra("title", title);
            intent.putExtra("text", text);

            PendingIntent pendingIntent = PendingIntent.getActivity(context, 0, intent, 0);
            try {
                pendingIntent.send();
            } catch (Throwable e) {

            }
        }
    }
}
