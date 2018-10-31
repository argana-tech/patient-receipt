package jp.ac.tottori_u.med.hosp.patient_reception;

import android.app.Application;

/**
 * Created by nishimura on 2018/09/21.
 */

public class MyApplication extends Application {
    private static MyApplication instance = null;
    private MyLifecycleHandler lifecycleHandler;
    @Override
    public void onCreate() {
        super.onCreate();

        instance = this;
        lifecycleHandler = new MyLifecycleHandler();
        registerActivityLifecycleCallbacks(lifecycleHandler);
    }

    public boolean isAppForeground(){
        return lifecycleHandler.isForeground();
    }

    public static MyApplication getInstance() {
        return instance;
    }
}
