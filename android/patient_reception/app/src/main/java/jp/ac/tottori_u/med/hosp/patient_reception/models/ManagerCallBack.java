package jp.ac.tottori_u.med.hosp.patient_reception.models;

/**
 * Created by nishimura on 2018/10/15.
 */

public interface ManagerCallBack<T> {

    public void callback(T resultObject);

}
