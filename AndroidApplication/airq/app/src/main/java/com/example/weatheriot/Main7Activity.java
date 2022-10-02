package com.example.weatheriot;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

public class Main7Activity extends AppCompatActivity {
    EditText url;
    Button btnSave;
    SharedPreferences sp;
    String urlSharedPref;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main7);

        url = findViewById(R.id.editurl);
        btnSave = findViewById(R.id.button8);
        sp = getSharedPreferences("URLprefs", Context.MODE_PRIVATE);
        btnSave.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                urlSharedPref = url.getText().toString();
                SharedPreferences.Editor editor = sp.edit();
                editor.putString("URL", urlSharedPref);
                editor.commit();
                Toast.makeText(Main7Activity.this, "URL Saved", Toast.LENGTH_SHORT).show();
                Intent main = new Intent(Main7Activity.this, MainActivity.class);
                startActivity(main);
            }
        });

    }
}