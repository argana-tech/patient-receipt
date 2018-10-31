package jp.ac.tottori_u.med.hosp.patient_reception;

import android.app.Activity;
import android.app.Application.ActivityLifecycleCallbacks;
import android.os.Bundle;

import jp.ac.tottori_u.med.hosp.patient_reception.activity.CommonActivity;

/**
 * Created by nishimura on 2018/10/18.
 */

public class MyLifecycleHandler implements ActivityLifecycleCallbacks {
    private boolean isForeground = false;

    @Override
    public void onActivityCreated(Activity activity, Bundle savedInstanceState) {

    }

    @Override
    public void onActivityStarted(Activity activity) {
    }

    @Override
    public void onActivityResumed(Activity activity) {
        if (activity instanceof CommonActivity) {
            isForeground = true;
        }
    }

    @Override
    public void onActivityPaused(Activity activity) {
        if (activity instanceof CommonActivity) {
            isForeground = false;
        }
    }

    @Override
    public void onActivityStopped(Activity activity) {
    }

    @Override
    public void onActivitySaveInstanceState(Activity activity, Bundle outState) {

    }

    @Override
    public void onActivityDestroyed(Activity activity) {

    }

    /**
     * アプリが前面にいるかどうかを取得します.
     * @return Foregroundにいたら<code>true</code>,backgroundにいたら<code>false</code>をかえします
     */
    public boolean isForeground() {
        return isForeground;
    }
}
