package jp.ac.tottori_u.med.hosp.patient_reception.models.object;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;

/**
 * Created by nishimura on 2018/10/16.
 */

public class LocateObject extends AbstractObject {
    @Override
    protected void setParameters(JSONObject json) throws JSONException {

    }


    private String uniqueId;

    public String getUniqueId() {
        return uniqueId;
    }

    public void setUniqueId(String uniqueId) {
        this.uniqueId = uniqueId;
    }

    public ArrayList<Beacon> getBeacons() {
        return beacons;
    }

    public void setBeacons(ArrayList<Beacon> beacons) {
        this.beacons = beacons;
    }

    private ArrayList<Beacon> beacons = new ArrayList<Beacon>();

    public Geo getGeo() {
        return geo;
    }

    public void setGeo(Geo geo) {
        this.geo = geo;
    }

    private Geo geo = new Geo();


    public static class Beacon {
        private String time;

        private static final String BEACON_LOCATIONS = "beacon_locations";

        public String getTime() {
            return time;
        }

        public void setTime(String time) {
            this.time = time;
        }

        public String getName() {
            return name;
        }

        public String getNameKey(int index) {
            return BEACON_LOCATIONS + "[" + index + "][name]";
        }
        public String getTimeKey(int index) {
            return BEACON_LOCATIONS + "[" + index + "][time]";
        }
        public String getLevelKey(int index) {
            return BEACON_LOCATIONS + "[" + index + "][level]";
        }


        public void setName(String name) {
            this.name = name;
        }

        public int getLevel() {
            return level;
        }

        public void setLevel(int level) {
            this.level = level;
        }

        private String name;
        private int level;
    }

    public static class Geo {

        private static final String GEO_LOCATIONS = "geo_locations";

        public double getLatitude() {
            return latitude;
        }


        public void setLatitude(double latitude) {
            this.latitude = latitude;
        }

        public double getLongitude() {
            return longitude;
        }

        public void setLongitude(double longitude) {
            this.longitude = longitude;
        }

        public String getLongitudeKey() {
            return GEO_LOCATIONS + "[lon]";
        }
        public String getLatitudeKey() {

            return GEO_LOCATIONS + "[lat]";
        }
        private double latitude;
        private double longitude;
    }
}
