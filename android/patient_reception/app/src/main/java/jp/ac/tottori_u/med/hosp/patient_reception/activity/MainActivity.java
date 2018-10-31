package jp.ac.tottori_u.med.hosp.patient_reception.activity;

import android.Manifest;
import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.bluetooth.BluetoothManager;
import android.bluetooth.le.BluetoothLeScanner;
import android.bluetooth.le.ScanCallback;
import android.bluetooth.le.ScanResult;
import android.bluetooth.le.ScanSettings;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.location.LocationProvider;
import android.os.Handler;
import android.os.Looper;
import android.provider.Settings;
import android.support.annotation.NonNull;
import android.support.v4.app.ActivityCompat;
import android.support.v4.app.NotificationManagerCompat;
import android.support.v4.content.ContextCompat;
import android.support.v7.app.AlertDialog;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import java.text.SimpleDateFormat;

import com.google.firebase.iid.FirebaseInstanceId;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.Timer;
import java.util.TimerTask;

//import jp.ac.tottori_u.med.hosp.patient_reception.Manifest;
import jp.ac.tottori_u.med.hosp.patient_reception.R;
import jp.ac.tottori_u.med.hosp.patient_reception.UserData;
import jp.ac.tottori_u.med.hosp.patient_reception.common.util.DateUtil;
import jp.ac.tottori_u.med.hosp.patient_reception.constants.IntentName;
import jp.ac.tottori_u.med.hosp.patient_reception.constants.MyColor;
import jp.ac.tottori_u.med.hosp.patient_reception.models.ManagerCallBack;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.CheckManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.LocateManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.NotificationManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.manager.SettingsManager;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.CheckObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.LocateObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.NotificationObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.ReceiptObject;
import jp.ac.tottori_u.med.hosp.patient_reception.models.object.SettingsObject;

import static com.google.zxing.integration.android.IntentIntegrator.REQUEST_CODE;

public class MainActivity extends CommonActivity implements LocationListener {

    private static String TAG = "MainActivity";

    private static final String  MSG_NOTIFICATION_UNAUTHORIZE = "通知設定が無効です。設定画面で通知設定を有効にしてください。";

    // Activity
    public static final int REQUEST_CODE_FROM_REGISTER = 10001;
    public static final int REQUEST_CODE_FROM_RECEPTION = 20001;
    public static final String RESULT_KEY_RESULT = "RESULT_KEY_RESULT";
    public static final String RESULT_KEY_CHECK_FLAG = "RESULT_KEY_CHECK_FLAG";
    public static final String RESULT_KEY_MESSAGE = "RESULT_KEY_MESSAGE";
    public static final String RESULT_KEY_FROM = "RESULT_KEY_FROM";

    private final int REQUEST_PERMISSION = 1000;

    private ProgressDialog progressDialog;

    //
    private TextView registerTextView;
    private TextView receptionTextView;
    private TextView possibbleReceptionTextView;
    private Button registerButton;
    private Button receptionButton;

    // トークン更新
    private BroadcastReceiver refreshTokenReceiver;
    private IntentFilter refreshTokenIntentFilter;

    // メッセージ受信
    private BroadcastReceiver messageReceiver;
    private IntentFilter messageIntentFilter;

    private boolean isOpendDialog = false;

    private SettingsObject settingsResult;

    private LocationManager locationManager;
    private static final int MinTime = 1000;
    private static final float MinDistance = 50;

    // BLE
    private BluetoothAdapter mBluetoothAdapter;
    private BluetoothLeScanner mBluetoothLeScanner;

//    private LocationManager mLocationManager;
//    private String bestProvider;

    private ArrayList<LocateObject.Beacon> beacons = new ArrayList<LocateObject.Beacon>();
    private Date beaconLastest;
    private LocateObject.Geo geo = new LocateObject.Geo();

    private Timer beaconTimer;
    private Timer geoTimer;
    private Timer receiptPossibleTimer;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        String fcmToken = FirebaseInstanceId.getInstance().getToken();
        Log.d(TAG, "fcmToken:" + fcmToken);

//        UserData.setUniqueId(this,"12345678901234");
//        String uniqueId = UserData.getUniqueId(this);

        // 権限確認
        authorize();

        // ウィジェットの設定
        // ※ 一番始めにやる
        setWidget();

        // レシーバーの設定
        setReceiber();

        // 画面表示の設定
//        setupDisplay();

        // API設定取得
        settings();

        // 位置情報のパーミッション
        checkPermission();


    }

//    /**
//     * 画面表示を設定する
//     */
//    private void setupDisplay() {
//        // 登録メッセージ
//        registerTextView.setText("");
//
//        // 受付メッセージ
//        receptionTextView.setText("");
//
//        // 受付可能時間
//        possibbleReceptionTextView.setText("");
//
//        // 登録ボタン
//        registerButton.setText("登録");
//        registerButton.setEnabled(false);
//
//        // 受付ボタン
//        receptionButton.
//        receptionButton.setEnabled(false);
//    }

    /**
     * 権限確認
     */
    private void authorize() {
        NotificationManagerCompat notificationManagerCompat = NotificationManagerCompat.from(this);
        boolean areNotificationsEnabled = notificationManagerCompat.areNotificationsEnabled();
        if (areNotificationsEnabled == false) {
            new AlertDialog.Builder(this)
                    .setTitle("通知設定")
                    .setMessage(MSG_NOTIFICATION_UNAUTHORIZE)
                    .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialogInterface, int i) {

                        }
                    }).show();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        switch (requestCode) {
            case (REQUEST_CODE_FROM_REGISTER):
                if (resultCode == RESULT_OK) {
                    boolean result = data.getBooleanExtra(RESULT_KEY_RESULT, false);
                    String message = data.getStringExtra(RESULT_KEY_MESSAGE);
                    String buttonName = result ? "再登録" : "登録";

                    settingRegisterButton(result, result, message, buttonName);
                } else {
                    // 失敗、何もしない



                }
                break;
            case (REQUEST_CODE_FROM_RECEPTION):
                if (resultCode == RESULT_OK) {
                    boolean result = data.getBooleanExtra(RESULT_KEY_RESULT, false);

                    String message = data.getStringExtra(RESULT_KEY_MESSAGE);
//                    String buttonName = result ? "再登録" : "登録";

                    if (result) {
                        boolean checkFlag = data.getBooleanExtra(RESULT_KEY_CHECK_FLAG, false);
                        CheckObject checkObject = new CheckObject();
                        checkObject.setResult(result);
                        checkObject.setCheckFlg(checkFlag);
                        checkObject.setButton("受付済み");
                        checkObject.setMessage(message);
                        receiptPossible(checkObject);
                    } else {

                    }
                } else {
                    // 失敗、何もしない



                }

                break;

        }
    }

    /**
     * BLEの設定
     * ビーコン
     */
    private void setupBLE() {
        BluetoothManager bluetoothManager = (BluetoothManager)getSystemService(Context.BLUETOOTH_SERVICE);

        // mBluetoothAdapterの取得
        mBluetoothAdapter = bluetoothManager.getAdapter();

        if (mBluetoothAdapter.isEnabled()) {
            // mBluetoothLeScannerの初期化
            mBluetoothLeScanner = mBluetoothAdapter.getBluetoothLeScanner();

            //        ScanFilter scanFilter = new ScanFilter.Builder().setServiceUuid(ParcelUuid.fromString(SERVICE_UUID)).build();
            ArrayList scanFilterList = new ArrayList();
            //        scanFilterList.add(scanFilter);
            ScanSettings scanSettings = new ScanSettings.Builder().setScanMode(ScanSettings.SCAN_MODE_BALANCED).build();


            startBLEScan();
        }



    }

    private void startBLEScan() {

        if (mBluetoothAdapter.isEnabled()) {
            mBluetoothLeScanner.startScan(new ScanCallback() {

                @Override
                public void onScanResult(int callbackType, ScanResult result) {
                    super.onScanResult(callbackType, result);

                    BluetoothDevice device = result.getDevice();
                    String name = device.getName();

                    if (name != null && "UM-50".equals(name)) {

                        if (beaconLastest == null) {
                            beaconLastest = new Date();
                        }

                        Date now = new Date();
                        long a = beaconLastest.getTime();
                        long b = now.getTime();
                        long diff = b - a;
                        if (settingsResult.getBeaconCollectSpan() * 1000 < diff) {
//                        Log.d(TAG, "address:" + result.getDevice().getAddress());
//                        Log.d(TAG, "RSSI:" + result.getRssi());

                            LocateObject.Beacon beacon = new LocateObject.Beacon();
                            beacon.setName(device.getAddress());
                            beacon.setTime(DateUtil.date2String(now, DateUtil.DateFormat.YYYYMMDD_HHMMSS));
                            beacon.setLevel(result.getRssi());
                            beacons.add(beacon);

                            beaconLastest = now;
                        }


                    }

                }

                @Override
                public void onBatchScanResults(List<ScanResult> results) {
                    super.onBatchScanResults(results);
                }

                @Override
                public void onScanFailed(int errorCode) {
                    super.onScanFailed(errorCode);
                }
            });


        }
   }

    private void stopBLEScan() {
        if (mBluetoothAdapter.isEnabled()) {
            mBluetoothLeScanner.stopScan(new ScanCallback() {
                @Override
                public void onScanResult(int callbackType, ScanResult result) {
                    super.onScanResult(callbackType, result);
                }

                @Override
                public void onBatchScanResults(List<ScanResult> results) {
                    super.onBatchScanResults(results);
                }

                @Override
                public void onScanFailed(int errorCode) {
                    super.onScanFailed(errorCode);
                }
            });
        }
    }



    // 位置情報許可の確認
    public void checkPermission() {
        // 既に許可している
        if (ContextCompat.checkSelfPermission(this,Manifest.permission.ACCESS_FINE_LOCATION)
                == PackageManager.PERMISSION_GRANTED) {

        }
        // 拒否していた場合
        else{
            requestLocationPermission();
        }
    }


    // 許可を求める
    private void requestLocationPermission() {
        if (ActivityCompat.shouldShowRequestPermissionRationale(this,
                Manifest.permission.ACCESS_FINE_LOCATION)) {
            ActivityCompat.requestPermissions(MainActivity.this,
                    new String[]{Manifest.permission.ACCESS_FINE_LOCATION},
                    REQUEST_PERMISSION);

        } else {
            Toast toast = Toast.makeText(this,
                    "許可されないとアプリが実行できません", Toast.LENGTH_SHORT);
            toast.show();

            ActivityCompat.requestPermissions(this,
                    new String[]{Manifest.permission.ACCESS_FINE_LOCATION,},
                    REQUEST_PERMISSION);

        }
    }

    // 結果の受け取り
    @Override
    public void onRequestPermissionsResult(int requestCode,
                                           @NonNull String[] permissions,
                                           @NonNull int[] grantResults) {
        if (requestCode == REQUEST_PERMISSION) {
            // 使用が許可された
            if (grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                setupLocationManager();

            } else {
                // それでも拒否された時の対応
                Toast toast = Toast.makeText(this,
                        "これ以上なにもできません", Toast.LENGTH_SHORT);
                toast.show();
            }
        }
    }


    @Override
    protected void onPause() {

        if (locationManager != null) {
            Log.d("LocationActivity", "locationManager.removeUpdates");
            // update を止める
            if (ActivityCompat.checkSelfPermission(this,
                    Manifest.permission.ACCESS_FINE_LOCATION) !=
                    PackageManager.PERMISSION_GRANTED){

                // TODO: Consider calling
                //    ActivityCompat#requestPermissions
                // here to request the missing permissions, and then overriding
                //   public void onRequestPermissionsResult(int requestCode, String[] permissions,
                //                                          int[] grantResults)
                // to handle the case where the user grants the permission. See the documentation
                // for ActivityCompat#requestPermissions for more details.
                return;
            }
            locationManager.removeUpdates(this);
        }

        super.onPause();
    }

    protected void startGPS() {


        Log.d("LocationActivity", "gpsEnabled");
        final boolean gpsEnabled
                = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);
        if (!gpsEnabled) {
            // GPSを設定するように促す
            enableLocationSettings();
        }

        if (locationManager != null) {
            Log.d("LocationActivity", "locationManager.requestLocationUpdates");

            try {
                // minTime = 1000msec, minDistance = 50m
                if (ActivityCompat.checkSelfPermission(this,
                        Manifest.permission.ACCESS_FINE_LOCATION) !=
                        PackageManager.PERMISSION_GRANTED){

                    // TODO: Consider calling
                    //    ActivityCompat#requestPermissions
                    // here to request the missing permissions, and then overriding
                    //   public void onRequestPermissionsResult(int requestCode, String[] permissions,
                    //                                          int[] grantResults)
                    // to handle the case where the user grants the permission. See the documentation
                    // for ActivityCompat#requestPermissions for more details.
                    return;
                }

                //locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, MinTime, MinDistance, this);
                locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 300, 1, this);
                locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 300, 1, this);
            } catch (Exception e) {
                e.printStackTrace();

                Toast toast = Toast.makeText(this,
                        "例外が発生、位置情報のPermissionを許可していますか？",
                        Toast.LENGTH_SHORT);
                toast.show();

                //MainActivityに戻す
                finish();
            }
        }

        super.onResume();
    }

    /**
     * 位置情報の設定
     * GPS
     */
    private void setupLocationManager () {
        locationManager = (LocationManager) getSystemService(LOCATION_SERVICE);
        startGPS();
    }


    /**
     * アプリの設定を取得する
     */
    private void settings() {
        SettingsManager settingsManager = new SettingsManager();
        settingsManager.getProperty(new ManagerCallBack<SettingsObject>() {
            @Override
            public void callback(SettingsObject resultObject) {
                Log.d(TAG, "BeaconSendSpan:" + resultObject.getBeaconSendSpan());
                settingsResult = resultObject;

                if (settingsResult.isResult()) {
                    if (settingsResult.getActiveSendTime().isPossible()) {
                        //
                        Log.e(TAG, "送信可能時間OK");

                        Handler  mHandler = new Handler(Looper.getMainLooper());
                        mHandler.post(new Runnable() {
                            @Override
                            public void run() {
                                // BLE
                                setupBLE();

                                // GPS
                                setupLocationManager();

                                // ビーコンタイマー
                                startBeaconTimer();

                                // 位置情報タイマー
                                startGeoTimer();

                                // 受付可否タイマー
                                startReceiptPossibleTimer();

                            }
                        });


                    } else {
                        // 位置情報送信NG
                        Log.e(TAG, "送信可能時間NG");

                    }

                    // タイマー


                    // 端末登録チェック
                    checkRegisterdDevice();

                    // 受付状況可否チェック
                    checkReceiptPossible();

                    // 画面情報更新
                    initDisplay();
                } else {
                    Handler  mHandler = new Handler(Looper.getMainLooper());
                    mHandler.post(new Runnable() {
                        @Override
                        public void run() {
                            new AlertDialog.Builder(MainActivity.this)
                                    .setTitle("エラー")
                                    .setMessage("設定情報が取得できませんでした。\nアプリを再起動してください。")
                                    .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                                        @Override
                                        public void onClick(DialogInterface dialogInterface, int i) {
                                            finish();
                                        }
                                    })
                                    .show();
                        }
                    });
                }



            }
        });
    }

    /**
     * ビーコンタイマー
     */
    private void startBeaconTimer() {

        if (settingsResult.isResult()) {
            Handler handler = new Handler();
            // タイマーを生成
            beaconTimer = new Timer(false);
            beaconTimer.schedule(new TimerTask() {
                                     @Override
                                     public void run() {
                                         //
                                         sendBeacon();

                                     }
                                 }, 0//
                    , settingsResult.getBeaconSendSpan() * 1000
            );
        }
    }


    private void stopBeaconTimer() {
        if (beaconTimer != null) {
            beaconTimer.cancel();
        }
    }

    private void stopGeoTimer() {
        if (geoTimer != null) {
            geoTimer.cancel();
        }
    }

    private void stopReceiptPossibleTimer() {
        if (receiptPossibleTimer != null) {
            receiptPossibleTimer.cancel();
        }
    }

    /**
     * ビーコン送信
     */
    private void sendBeacon () {
        final LocateObject locateObject = new LocateObject();
        locateObject.setUniqueId(UserData.getUniqueId(this));
        locateObject.setBeacons(beacons);
        LocateManager locateManager = new LocateManager();
        locateManager.postBeacon(locateObject, new ManagerCallBack<LocateObject>() {

            @Override
            public void callback(LocateObject resultObject) {
                Log.d(TAG, "ビーコン情報送信:" + resultObject.isResult() );
            }

        });

        beacons = new ArrayList<LocateObject.Beacon>();
    }

    /**
     * 位置情報タイマー
     */
    private void startGeoTimer() {
        if (settingsResult.isResult()) {
            Handler handler = new Handler();
            geoTimer = new Timer(false);
            geoTimer.schedule(new TimerTask() {
                                  @Override
                                  public void run() {
                                      //
                                      sendGeo();

                                  }
                              }, 0
                    , settingsResult.getGeoSendSpan() * 1000
            );
        }
    }

    /**
     * 位置情報送信
     */
    private void sendGeo () {
        if (geo != null) {
            final LocateObject locateObject = new LocateObject();
            locateObject.setUniqueId(UserData.getUniqueId(this));
            locateObject.setGeo(geo);
            LocateManager locateManager = new LocateManager();
            locateManager.postGeo(locateObject, new ManagerCallBack<LocateObject>() {

                @Override
                public void callback(LocateObject resultObject) {
                    Log.d(TAG, "位置情報送信結果:" + resultObject.isResult() );
                }

            });

            geo = null;
        }


    }


    /**
     * 受付可否タイマー
     */
    private void startReceiptPossibleTimer() {
        if (settingsResult.isResult()) {
            Handler handler = new Handler();

            receiptPossibleTimer = new Timer(false);
            receiptPossibleTimer.schedule(new TimerTask() {
                                              @Override
                                              public void run() {
                                                  checkReceiptPossible();
                                              }
                                          }, 0,
                    settingsResult.getMenuCheckSpan() * 1000
            );
        }
    }


    /**
     * 端末登録チェック
     */
    private void checkRegisterdDevice() {
        CheckObject checkObject = new CheckObject();
        String uniqueId = UserData.getUniqueId(this);
        checkObject.setUniqueId(uniqueId);
        new CheckManager().postRegister(checkObject, new ManagerCallBack<CheckObject>() {
            @Override
            public void callback(CheckObject resultObject) {

                settingRegisterButton(
                        resultObject.isResult(),
                        resultObject.isCheckFlg(),
                        resultObject.getMessage(),
                        resultObject.getButton());
            }
        });
    }

    /**
     * 登録ボタンの状態とメッセージを変更する
     * @param result APIの結果
     * @param checkFlg ボタンの活性
     * @param message メッセージ
     * @param button ボタンの表示名
     */
    private void settingRegisterButton(boolean result, final boolean checkFlg, String message, String button) {

        if (result) {
            // 登録完了
            registerButton.setText(button);
        } else {
            // 登録完了
            registerButton.setText("登録");
        }

        //
        Handler  mHandler = new Handler(Looper.getMainLooper());
        mHandler.post(new Runnable() {
            @Override
            public void run() {

                if (checkFlg) {
                    // 有効
                    registerButton.setBackgroundColor(MyColor.DARK_ORENGE);
                    registerButton.setEnabled(true);
                } else {
                    registerButton.setBackgroundColor(Color.GRAY);
                    registerButton.setEnabled(false);
                }
            }
        });

        // メッセージの変更
        registerTextView.setText(message);
        try {

        } catch (Exception e) {
            Log.e(TAG, e.getLocalizedMessage());
        }

        Log.d(TAG, "登録確認完了");
    }

    /**
     * 受付可能チェック
     */
    private void checkReceiptPossible() {
        CheckObject checkObject = new CheckObject();
        String uniqueId = UserData.getUniqueId(this);
        checkObject.setUniqueId(uniqueId);
        new CheckManager().postRecipet(checkObject, new ManagerCallBack<CheckObject>() {
            @Override
            public void callback(CheckObject resultObject) {

                receiptPossible(resultObject);
            }
        });
    }

    /**
     * 受付可否チェック表示変更
     * @param resultObject
     */
    private void receiptPossible(final CheckObject resultObject) {
        if (resultObject.isResult()) {
            // OK
            Handler  mHandler = new Handler(Looper.getMainLooper());
            mHandler.post(new Runnable() {
                @Override
                public void run() {
                    try {
                        Log.d(TAG, "受付可能チェック: " + resultObject.isCheckFlg());
                        receptionTextView.setText(resultObject.getMessage());
                        receptionButton.setText(resultObject.getButton());
                        receptionButton.setEnabled(resultObject.isCheckFlg());
                        if (resultObject.isCheckFlg()) {
                            receptionButton.setBackgroundColor(MyColor.DARK_GREEN);
                        } else {
                            receptionButton.setBackgroundColor(MyColor.DARK_GRAY);
                        }
                    } catch (Exception ex) {

                        Log.e(TAG, ex.getLocalizedMessage());
                    }
                }
            });

        } else {
            // NG
            Handler  mHandler = new Handler(Looper.getMainLooper());
            mHandler.post(new Runnable() {
                @Override
                public void run() {
                    try {
                        Log.d(TAG, "受付可能チェック: " + resultObject.isCheckFlg());
                        receptionTextView.setText(resultObject.getMessage());
                        receptionButton.setText(resultObject.getButton());
                        receptionButton.setEnabled(false);
                        receptionButton.setBackgroundColor(MyColor.DARK_GRAY);
                    } catch (Exception ex) {

                        Log.e(TAG, ex.getLocalizedMessage());
                    }
                }
            });
        }
    }

    /**
     * 画面設定
     */
    private void initDisplay() {
        if (settingsResult.isResult()) {
            possibbleReceptionTextView.setText("受付可能時間: " + settingsResult.getActiveReceiptTime().getStart() + " ~ " + settingsResult.getActiveReceiptTime().getEnd());
        } else {
            possibbleReceptionTextView.setText("");
        }
    }





    /**
     *
     */
    private void setReceiber() {
        // トークン発行レシーバー
        refreshTokenReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
//                register();
            }
        };
        refreshTokenIntentFilter = new IntentFilter();
        refreshTokenIntentFilter.addAction(IntentName.REFRESH_TOKEN);
        registerReceiver(refreshTokenReceiver, refreshTokenIntentFilter);

        // メッセージの受信
        messageReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                Bundle bundle = intent.getExtras();
                String title = bundle.getString("title");
                String text = bundle.getString("text");
                popUpWindow(title, text);
            }
        };
        messageIntentFilter = new IntentFilter();
        messageIntentFilter.addAction(IntentName.RECIVE_MESSAGE);
        registerReceiver(messageReceiver, messageIntentFilter);
    }


    /**
     * ウィジェットの設定
     */
    private void setWidget() {

        // 登録ボタン
        registerButton = (Button) findViewById(R.id.registerButton);
        registerButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Log.d(TAG, " 登録ボタンクリック");
                Intent intent = new Intent(getApplication(), RegisterActivity.class);
                startActivityForResult(intent, REQUEST_CODE_FROM_REGISTER);
            }
        });
        registerButton.setBackgroundColor(Color.GRAY);
        registerButton.setText("登録");
        registerButton.setEnabled(false);


        // 受付ボタン
        receptionButton = (Button) findViewById(R.id.receptionButton);
        receptionButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Log.d(TAG, " 受付ボタンクリック");
                Intent intent = new Intent(getApplication(), ReceptionActivity.class);
//                startActivity(intent);
                startActivityForResult(intent, REQUEST_CODE_FROM_RECEPTION);
            }

        });
        receptionButton.setBackgroundColor(Color.GRAY);
        receptionButton.setText("受付");
        receptionButton.setEnabled(false);

        // 登録テキスト
        registerTextView = (TextView) findViewById(R.id.registerTextView);
        registerTextView.setText("");

        // 受付テキスト
        receptionTextView = (TextView) findViewById(R.id.receptionTextView);
        receptionTextView.setText("");

        // 受付可能テキスト
        possibbleReceptionTextView = (TextView) findViewById(R.id.possibbleReceptionTextView);
        possibbleReceptionTextView.setText("");
    }

    /**
     * ポップアップウィンドウ
     * @param title
     * @param text
     */
    private void popUpWindow(String title, String text) {

//        mp.start();

        if (isOpendDialog == false) {
            new AlertDialog.Builder(this)
                    .setTitle(title)
                    .setMessage(text)
                    .setPositiveButton("OK", new DialogInterface.OnClickListener() {
                        @Override
                        public void onClick(DialogInterface dialogInterface, int i) {
                            // 既読処理
                            read();
                            isOpendDialog = false;

                        }
                    })
                    .show();
            //
            isOpendDialog = true;
        }

    }

    /**
     * 既読処理
     */
    private void read() {
        final NotificationObject notificationObject = new NotificationObject();
        notificationObject.setUniqueId(UserData.getUniqueId(this));
        new NotificationManager().postRead(notificationObject, new ManagerCallBack<NotificationObject>() {
            @Override
            public void callback(NotificationObject resultObject) {
                if (notificationObject.isResult()) {
                    Log.d(TAG, "既読処理:OK");
                } else {
                    Log.d(TAG, "既読処理:NG");
                }
            }
        }


        );
    }

    @SuppressLint("WrongConstant")
    @Override
    public void onLocationChanged(Location location) {

        LocateObject.Geo geo = new LocateObject.Geo();
        geo.setLatitude(location.getLatitude());
        geo.setLongitude(location.getLongitude());

    }



    @Override
    public void onProviderDisabled(String provider) {
        Log.d("DEBUG", "called onProviderDisabled");
    }

    @Override
    public void onProviderEnabled(String provider) {
        Log.d("DEBUG", "called onProviderEnabled");
    }

    @Override
    public void onStatusChanged(String provider, int status, Bundle extras) {
        switch (status) {
            case LocationProvider.AVAILABLE:
                Log.d(TAG, "LocationProvider.AVAILABLE\n");
                break;
            case LocationProvider.OUT_OF_SERVICE:
                Log.d(TAG, "LocationProvider.OUT_OF_SERVICE\n");
                break;
            case LocationProvider.TEMPORARILY_UNAVAILABLE:
                Log.d(TAG,"LocationProvider.TEMPORARILY_UNAVAILABLE\n");
                break;
        }
    }

    private void enableLocationSettings() {
        Intent settingsIntent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
        startActivity(settingsIntent);
    }

    private void stopGPS(){
        if (locationManager != null) {
            Log.d("LocationActivity", "onStop()");
            Log.d(TAG,"stopGPS\n");

            // update を止める
            if (ActivityCompat.checkSelfPermission(this,
                    Manifest.permission.ACCESS_FINE_LOCATION) !=
                    PackageManager.PERMISSION_GRANTED &&
                    ActivityCompat.checkSelfPermission(this,
                            Manifest.permission.ACCESS_COARSE_LOCATION) !=
                            PackageManager.PERMISSION_GRANTED) {
                return;
            }
            locationManager.removeUpdates(this);
        }
    }

    @Override
    public void onStop() {
        super.onStop();
        stopGPS();
        stopBLEScan();

        stopBeaconTimer();
        stopGeoTimer();
        stopReceiptPossibleTimer();
    }

    @Override
    protected void onRestart() {
        super.onRestart();

        startGPS();
        startBLEScan();
        startBeaconTimer();
        startGeoTimer();
        startReceiptPossibleTimer();

    }
}
