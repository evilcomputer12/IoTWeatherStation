package com.example.weatheriot;

public class lista {
    private String id;
    private String temp;
    private String hum;
    private String pm10;
    private String pm25;
    private String bat_vol;
    private String sol_vol;
    private String vreme;

    public lista(){}
    public lista(String id, String temp, String hum, String pm10, String pm25, String bat_vol, String sol_vol, String vreme){
        this.id = id;
        this.temp = temp;
        this.hum = hum;
        this.pm10 = pm10;
        this.pm25 = pm25;
        this.bat_vol = bat_vol;
        this.sol_vol = sol_vol;
        this.vreme = vreme;
    }
    public String getId() {
        return id;
    }

    public void setId(String title) {
        this.id = id;
    }

    public String getTemp() {
        return temp;
    }

    public void setTemp(String temp) {
        this.temp = temp;
    }

    public String getHum() {
        return hum;
    }

    public void setHum(String hum) {
        this.hum = hum;
    }

    public String getPm10() {
        return pm10;
    }

    public void setPm10(String pm10) {
        this.pm10 = pm10;
    }

    public String getPm25() {
        return pm25;
    }

    public void setPm25(String pm25) {
        this.pm25 = pm25;
    }

    public String getVreme() {
        return vreme;
    }

    public void setVreme(String vreme) {
        this.vreme = vreme;
    }

    public String getBat_vol() {
        return bat_vol;
    }

    public void setBat_vol(String bat_vol) {
        this.bat_vol = bat_vol;
    }

    public String getSol_vol() {
        return sol_vol;
    }

    public void setSol_vol(String bat_vol) {
        this.sol_vol = bat_vol;
    }
}
