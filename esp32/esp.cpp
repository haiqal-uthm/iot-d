#include "esp_camera.h"
#include <WiFi.h>
#include <HTTPClient.h>
#include "esp_timer.h"
#include "img_converters.h"
#include "Arduino.h"
#include "soc/soc.h"
#include "soc/rtc_cntl_reg.h"

// Pin definitions for CAMERA_MODEL_AI_THINKER
#define PWDN_GPIO_NUM     32
#define RESET_GPIO_NUM    -1
#define XCLK_GPIO_NUM      0
#define SIOD_GPIO_NUM     26
#define SIOC_GPIO_NUM     27
#define Y9_GPIO_NUM       35
#define Y8_GPIO_NUM       34
#define Y7_GPIO_NUM       39
#define Y6_GPIO_NUM       36
#define Y5_GPIO_NUM       21
#define Y4_GPIO_NUM       19
#define Y3_GPIO_NUM       18
#define Y2_GPIO_NUM        5
#define VSYNC_GPIO_NUM    25
#define HREF_GPIO_NUM     23
#define PCLK_GPIO_NUM     22

#define VIBRATION_SENSOR_PIN 13

const char* ssid = "qal";
const char* password = "ayammasbro";

// Separate IP and port
const char* serverIP = "172.20.10.2";
const int serverPort = 3000;
const char* uploadPath = "/upload"; // Endpoint path

String getServerUrl() {
  return "http://" + String(serverIP) + ":" + String(serverPort) + String(uploadPath);
}

void uploadImage(uint8_t *image_data, size_t image_size) {
  HTTPClient http;

  String serverUrl = getServerUrl();

  Serial.print("Connecting to server: ");
  Serial.println(serverUrl);

  // Begin HTTP connection
  if (!http.begin(serverUrl)) {
    Serial.println("HTTP begin failed!");
    return;
  } else {
    Serial.println("HTTP begin succeeded.");
  }
  http.addHeader("Content-Type", "image/jpeg");

  Serial.print("Sending POST request...");
  int httpResponseCode = http.POST(image_data, image_size);

  if (httpResponseCode > 0) {
    Serial.print("HTTP POST success, code: ");
    Serial.println(httpResponseCode);
    String response = http.getString();
    Serial.println("Server response:");
    Serial.println(response);
  } else {
    Serial.print("Error on sending POST: ");
    Serial.println(httpResponseCode);
  }

  http.end();
}

void setup() {
  WRITE_PERI_REG(RTC_CNTL_BROWN_OUT_REG, 0);
  Serial.begin(115200);
  Serial.println();

  pinMode(VIBRATION_SENSOR_PIN, INPUT);

  camera_config_t config;
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer = LEDC_TIMER_0;
  config.pin_d0 = Y2_GPIO_NUM;
  config.pin_d1 = Y3_GPIO_NUM;
  config.pin_d2 = Y4_GPIO_NUM;
  config.pin_d3 = Y5_GPIO_NUM;
  config.pin_d4 = Y6_GPIO_NUM;
  config.pin_d5 = Y7_GPIO_NUM;
  config.pin_d6 = Y8_GPIO_NUM;
  config.pin_d7 = Y9_GPIO_NUM;
  config.pin_xclk = XCLK_GPIO_NUM;
  config.pin_pclk = PCLK_GPIO_NUM;
  config.pin_vsync = VSYNC_GPIO_NUM;
  config.pin_href = HREF_GPIO_NUM;
  config.pin_sscb_sda = SIOD_GPIO_NUM;
  config.pin_sscb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn = PWDN_GPIO_NUM;
  config.pin_reset = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.pixel_format = PIXFORMAT_JPEG;

  if(psramFound()){
    config.frame_size = FRAMESIZE_VGA;
    config.jpeg_quality = 10;
    config.fb_count = 2;
  } else {
    config.frame_size = FRAMESIZE_SVGA;
    config.jpeg_quality = 12;
    config.fb_count = 1;
  }

  esp_err_t err = esp_camera_init(&config);
  if (err != ESP_OK) {
    Serial.printf("Camera init failed with error 0x%x\n", err);
    delay(1000);
    ESP.restart();
  }

  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");
  int wifiTries = 0;
  while (WiFi.status() != WL_CONNECTED && wifiTries++ < 60) {
    delay(500);
    Serial.print(".");
  }
  if(WiFi.status() == WL_CONNECTED){
    Serial.println();
    Serial.print("ESP32-CAM IP Address: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println();
    Serial.println("Failed to connect to WiFi!");
  }
}

void loop() {
  if (digitalRead(VIBRATION_SENSOR_PIN) == LOW) {

    delay(2000);
    Serial.println("Vibration detected! Taking picture...");

    camera_fb_t * fb = esp_camera_fb_get();

    if (!fb) {
      Serial.println("Camera capture failed");
      delay(1000);
      return;
    }

    Serial.print("Image size: ");
    Serial.println(fb->len);

    Serial.println("Uploading image to server...");

    if (WiFi.status() == WL_CONNECTED) {
      uploadImage(fb->buf, fb->len);
    } else {
      Serial.println("WiFi disconnected. Reconnecting...");
      WiFi.begin(ssid, password);
      int attempts = 0;
      while (WiFi.status() != WL_CONNECTED && attempts < 20) {
        delay(500);
        Serial.print(".");
        attempts++;
      }
      if (WiFi.status() == WL_CONNECTED) {
        uploadImage(fb->buf, fb->len);
      } else {
        Serial.println("Failed to reconnect to WiFi");
      }
    }

    esp_camera_fb_return(fb);
    delay(3000);
  }
}