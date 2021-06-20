package com.example.beehivecontrol;

import android.app.AlertDialog;
import android.content.Context;
import android.os.Bundle;
import android.webkit.JsResult;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;

import androidx.appcompat.app.AppCompatActivity;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        final Context myApp = this;

        WebView browser = (WebView) findViewById(R.id.mainWebView);
        browser.setWebViewClient(new WebViewClient()); // Open urls inside browser
        browser.getSettings().setDomStorageEnabled(true); // Enable local storage
        browser.setVerticalScrollBarEnabled(true); // Enable vertical scrolling
        browser.getSettings().setJavaScriptEnabled(true); // Enable javascript

        // Turn JS alerts into alertboxes
        browser.setWebChromeClient(new WebChromeClient() {
            @Override
            public boolean onJsAlert(WebView view, String url, String message, JsResult result) {
                new AlertDialog.Builder(myApp)
                        .setTitle("Beehive Alert")
                        .setMessage(message)
                        .setPositiveButton(android.R.string.ok, (dialog, which) -> result.confirm())
                        .setCancelable(false)
                        .create()
                        .show();
                return true;
            }
        });

        browser.loadUrl("http://ec2-100-24-224-29.compute-1.amazonaws.com/beehive");
    }

}