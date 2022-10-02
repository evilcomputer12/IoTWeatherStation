package com.example.weatheriot;

import android.app.ActionBar;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;


import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;


import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonArrayRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

public class MainActivity2 extends AppCompatActivity {
    RecyclerView recyclerView;
    List<lista> listi;
    Adapter adapter;
    SwipeRefreshLayout swipeLayout;
    SharedPreferences sharedpreferences2;
    String url_get2;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main2);
        ActionBar actionBar = (ActionBar) getActionBar();
        if(actionBar != null) {
            actionBar.setDisplayHomeAsUpEnabled(true);
            extractListi();
        }
        swipeLayout = findViewById(R.id.swipe_container);
        // Adding Listener
        swipeLayout.setOnRefreshListener(new SwipeRefreshLayout.OnRefreshListener() {
            @Override
            public void onRefresh() {
                // Your code here
                // To keep animation for 4 seconds
                recyclerView = findViewById(R.id.lista);
                listi = new ArrayList<>();
                extractListi();
                new Handler().postDelayed(new Runnable() {
                    @Override public void run() {
                        // Stop animation (This will be after 3 seconds)
                        swipeLayout.setRefreshing(false);
                    }
                }, 4000); // Delay in millis
            }
        });
    }

    private void extractListi() {
        RequestQueue queue = Volley.newRequestQueue(MainActivity2.this);
        sharedpreferences2 = getSharedPreferences("URLprefs",
                Context.MODE_PRIVATE);
        url_get2 = sharedpreferences2.getString("URL", "");
        String url = "https://"+url_get2+"/history.php?api_key=JUlzGXNaj7BJe";
        JsonArrayRequest jsonArrayRequest = new JsonArrayRequest(Request.Method.GET, url, null, new Response.Listener<JSONArray>() {
            @Override
            public void onResponse(JSONArray response) {
                for (int i = 0; i < response.length(); i++) {
                    try {
                        JSONObject listaObject = response.getJSONObject(i);
                        lista lista = new lista();
                        lista.setId(listaObject.getString("id"));
                        lista.setTemp(listaObject.getString("temp_val"));
                        lista.setHum(listaObject.getString("hum_val"));
                        lista.setPm10(listaObject.getString("pm25_val"));
                        lista.setPm25(listaObject.getString("pm10_val"));
                        lista.setBat_vol(listaObject.getString("vbat_val"));
                        lista.setSol_vol(listaObject.getString("vsol_val"));
                        lista.setVreme(listaObject.getString("reading_time"));
                        listi.add(lista);

                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }

                recyclerView.setLayoutManager(new LinearLayoutManager(getApplicationContext()));
                adapter = new Adapter(getApplicationContext(),listi);
                recyclerView.setAdapter(adapter);
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.d("tag", "onErrorResponse: " + error.getMessage());
            }
        });

        queue.add(jsonArrayRequest);

    }

}