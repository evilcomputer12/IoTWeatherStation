package com.example.weatheriot;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.app.AppCompatDelegate;
import androidx.core.app.NotificationCompat;
import androidx.core.app.NotificationManagerCompat;

import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.media.MediaPlayer;
import android.net.ConnectivityManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.PowerManager;
import android.os.Vibrator;
import android.provider.Settings;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

public class MainActivity extends AppCompatActivity {


    private TextView textView;
    private TextView temperature;
    private TextView humidity;
    private TextView pm_10_text;
    private TextView airquality_text;
    private TextView lst_update;
    private ProgressBar pm_10pg;
    private ProgressBar airquality;
    private TextView voltupd;
    SharedPreferences sharedpreferences;
    String url_get;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES); //For night mode theme
        setContentView(R.layout.activity_main);
        boolean alreadyExecuted = false;
        getData();
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            data();
            dooze();
        }
        alreadyExecuted = true;
        if(Build.VERSION.SDK_INT >= Build.VERSION_CODES.O){
            NotificationChannel channel = new NotificationChannel("Alarm","Alarm", NotificationManager.IMPORTANCE_DEFAULT);
            NotificationManager manager = getSystemService(NotificationManager.class);
            manager.createNotificationChannel(channel);
        }
        TextView tempIcon, humIcon;
        Typeface typeface;

        tempIcon = (TextView) findViewById(R.id.tempIcon);
        humIcon = (TextView) findViewById(R.id.humIcon);

        typeface = Typeface.createFromAsset(getAssets(), "fonts/fontawesome-webfont.ttf");

        tempIcon.setTypeface(typeface);
        humIcon.setTypeface(typeface);
    }

    private void dooze() {
        PowerManager pm = (PowerManager) getSystemService(Context.POWER_SERVICE);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (!pm.isIgnoringBatteryOptimizations(getPackageName())) {
                Intent intent = new Intent(Settings.ACTION_REQUEST_IGNORE_BATTERY_OPTIMIZATIONS, Uri.parse("package:" + getPackageName()));
                startActivity(intent);
            }
        }
    }
    private void data(){
        ConnectivityManager connMgr = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            if(connMgr.getRestrictBackgroundStatus()== ConnectivityManager.RESTRICT_BACKGROUND_STATUS_ENABLED || connMgr.getRestrictBackgroundStatus()== ConnectivityManager.RESTRICT_BACKGROUND_STATUS_WHITELISTED) {
                //Toast.makeText(MainActivity.this,String.valueOf(connMgr.getRestrictBackgroundStatus()), Toast.LENGTH_LONG).show();
                Intent intent = new Intent(Settings.ACTION_IGNORE_BACKGROUND_DATA_RESTRICTIONS_SETTINGS, Uri.parse("package:" + getPackageName()));
                startActivity(intent);
            }
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.menu, menu);

        return true;
    }
    public boolean onOptionsItemSelected(MenuItem item){
        int id = item.getItemId();
        if(id == R.id.item2){
            Intent intent1 = new Intent(MainActivity.this, com.example.weatheriot.MainActivity2.class);
            startActivity(intent1);
        }
        else if(id == R.id.item3){
            Intent intent2 = new Intent(MainActivity.this, com.example.weatheriot.ActivityMain3.class);
            startActivity(intent2);
        }
        else if(id == R.id.item4){
            Intent intent3 = new Intent(MainActivity.this, com.example.weatheriot.Main7Activity.class);
            startActivity(intent3);
        }

        return super.onOptionsItemSelected(item);
    }

    private void getData() {
        RequestQueue queue = Volley.newRequestQueue(this);
        sharedpreferences = getSharedPreferences("URLprefs",
                Context.MODE_PRIVATE);
        url_get = sharedpreferences.getString("URL", "");
        String url = "https://"+url_get+"/get-data.php?api_key=JUlzGXNaj7BJe";
        JsonArrayRequest jsonArrayRequest = new JsonArrayRequest(url, new Response.Listener<JSONArray>() {
            @Override
            public void onResponse(JSONArray response) {
                String temp = "";
                String hum = "";
                String vbat = "";
                String vsol = "";
                String pm_10 = "";
                String voltup = "";
                String last_update = "";
                String sensor_data = "";
                String pm2_5 = "";
                try {
                    JSONObject data = response.getJSONObject(0);
                    temp = data.getString("temp_val");
                    hum =  data.getString("hum_val");
                    pm2_5= data.getString("pm25_val");
                    pm_10 = data.getString("pm10_val");
                    vbat = data.getString("vbat_val");
                    vsol = data.getString("vsol_val");
                    last_update = data.getString("reading_time");
                } catch (JSONException e) {
                    e.printStackTrace();
                }

                Toast.makeText(MainActivity.this,last_update, Toast.LENGTH_LONG).show();
                final MediaPlayer alarm1 = MediaPlayer.create(MainActivity.this, R.raw.alarm3);
                alarm1.start();
                humidity = (TextView) findViewById(R.id.hum);
                humidity.setText(hum+ "%");
                temperature = (TextView) findViewById(R.id.temp);
                temperature.setText(temp+ "°C");
                pm_10_text = (TextView) findViewById(R.id.co2ppm_text);
                pm_10_text.setText(pm_10+ " ug/m3");
                pm_10pg = (ProgressBar) findViewById(R.id.co2ppm);
                pm_10pg.setMax(500);
                int pm_10_int = Integer.parseInt(String.valueOf(Math.round(Float.parseFloat(pm_10))));
                pm_10pg.setProgress(pm_10_int);
                airquality = (ProgressBar) findViewById(R.id.airquality);
                airquality.setMax(500);
                int pm_25_int = Integer.parseInt(String.valueOf(Math.round(Float.parseFloat(pm2_5))));
                airquality.setProgress(pm_25_int);
                airquality_text = (TextView) findViewById(R.id.airquality_text);
                airquality_text.setText(pm2_5+ " ug/m3");
                lst_update = (TextView) findViewById(R.id.lst_update);
                lst_update.setText("Last Update: " + last_update);
                voltupd = (TextView) findViewById(R.id.voltage_upd);
                voltup = "Bat V="+ vbat +"(mV), "+"Sol V="+vsol+"(mV)";
                voltupd.setText(voltup);
                if(pm_10_int > 100 || pm_25_int >100){
                    final MediaPlayer alarm = MediaPlayer.create(MainActivity.this, R.raw.alarm);
                    Vibrator v = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
                    v.vibrate(400);
                    alarm.start();
                    NotificationCompat.Builder builder = new NotificationCompat.Builder(MainActivity.this, "Alarm");
                    builder.setContentTitle("Alarm");
                    builder.setContentText("Bad Air Quality");
                    builder.setSmallIcon(R.drawable.ic_stat_new_releases);
                    Context context = getApplicationContext();
                    Intent notIntent = new Intent(context, MainActivity.class);
                    PendingIntent contentIntent = PendingIntent.getActivity(MainActivity.this,0, notIntent,0);
                    builder.setContentIntent(contentIntent);
                    builder.setAutoCancel(true);
                    NotificationManagerCompat managerCompat = NotificationManagerCompat.from(MainActivity.this);
                    managerCompat.notify(1, builder.build());
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                        context.startForegroundService(notIntent);
                    }
                }
                if(Integer.parseInt(vbat) < 3680){
                    final MediaPlayer alarm = MediaPlayer.create(MainActivity.this, R.raw.alarm);
                    Vibrator v = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
                    v.vibrate(400);
                    alarm.start();
                    NotificationCompat.Builder builder = new NotificationCompat.Builder(MainActivity.this, "Alarm");
                    builder.setContentTitle("Alarm");
                    builder.setContentText("Measuring Device Low Battery");
                    builder.setSmallIcon(R.drawable.ic_stat_new_releases);
                    Context context = getApplicationContext();
                    Intent notIntent = new Intent(context, MainActivity.class);
                    PendingIntent contentIntent = PendingIntent.getActivity(MainActivity.this,0, notIntent,0);
                    builder.setContentIntent(contentIntent);
                    builder.setAutoCancel(true);
                    NotificationManagerCompat managerCompat = NotificationManagerCompat.from(MainActivity.this);
                    managerCompat.notify(1, builder.build());
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                        context.startForegroundService(notIntent);
                    }
                }


            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Toast.makeText(MainActivity.this, "Connection Error", Toast.LENGTH_LONG).show();
            }
        });
        queue.add(jsonArrayRequest);
        refresh(600000);
    }

    private void refresh(int milliseconds) {
        final Handler handler = new Handler();
        final Runnable runnable = new Runnable() {
            @Override
            public void run() {
                getData();

            }
        };
        handler.postDelayed(runnable, milliseconds);
    }



}

