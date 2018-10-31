package jp.ac.tottori_u.med.hosp.patient_reception.common.util;

import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Created by nishimura on 2018/10/17.
 */

public class DateUtil {

    public enum DateFormat {
        // GPS
        YYYYMMDD_HHMMSS("yyyy-MM-dd HH:mm:ss");

        private final String text;

        private DateFormat(final String text) {
            this.text = text;
        }

        public String getString() {
            return this.text;
        }
    }


    public static String date2String (Date date, DateFormat format) {
        String str = new SimpleDateFormat(format.getString()).format(date);
        return str;
    }

}
