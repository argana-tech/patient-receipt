package jp.ac.tottori_u.med.hosp.patient_reception.constants;

/**
 * Created by nishimura on 2018/10/03.
 */

public class Server {

    // 接続先
    public static final String HOST = "192.168.1.3";

    // API
    public enum  API {

        // 設定取得
        SETTINGS_PROPERTY("settings/property"),

        /* CHECK */
        // 登録端末チェッック
        CHECK_REGISTER("check/register"),

        // 受付チェッック
        CHECK_TIME("check/time"),

        /* Receipt */
        // 登録端末チェッック
        RECEIPT_REGISTER("receipt/register"),

        // 受付チェッック
        RECEIPT_ENTRY("receipt/entry"),

        /* 通知 */
        // 既読
        NOTIFICATION_READ("notification/read"),

        /* 位置情報 */
        // ビーコン
        LOCATE_BEACON("locate/add_beacon"),

        // GPS
        LOCATE_GEO("locate/add_geo"),


        ;


        private final String text;

        private API(final String text) {
            this.text = text;
        }

        public String getString() {
            return "/api/" + this.text;
        }
    }

//    enum API {
//        enum Check {
//            register();
//            time();
//        }
//        private API() {
//
//        }
//
//        public String getAPI() {
//
//            return "";
//        }
//    }

//    enum check {
//        register,
//        time
//
//
//    }
//    class API {
//
//        class Check {
//
//            check action;
//            public Check (check action) {
//                this.
//            }
//        }
//
//        class Receipt {
//
//        }
//    }
//
//    interface Colored {
//        String getURL();
//    }

//    public enum API implements Colored {
//
//        @Override public String;
//        private API() {
//
//        }
//        @Override
//        public String getURL() {
//            return null;
//        }
//    }

//    public enum API {
//        Check("check");
//        private String controller;
//        private API(String controller) {
//            this.controller = controller;
//        }
//
//        public enum Controller {
//
//        }
//
////
//        public enum Check {
//            Register("register");
//
//            private String contrller = "check";
//            private String action;
//            private Check (String action) {
//                this.action = action;
//            }
//
//
//
//        }
//
//        public String getURL(API.Controller controller) {
////            return contrller + "/" + action;
//
//        }
//
//    }

    public enum Fruit {
        Orange("Ehime"),
        Apple("Aomori"),
        Melon("Ibaraki");

        // フィールドの定義
        private String name;

        // コンストラクタの定義
        private Fruit(String name) {
            this.name = name;
        }


    }

//    public class API {
//
//        public class Check {
//            private static String register = "register";
//        }
//    }

//    public enum API {
//
//        Check("check");
//
//        private final String controller;
//
//
//        private API(String controller) {
//            this.controller = controller;
//        }
//
//        private enum Check {
//
//        }
//
//    }
}
