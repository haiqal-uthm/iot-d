<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index()
    {
        return view('weather');
    }

    public function fetchWeather()
{
    $latitude = '3.8581089';
    $longitude = '101.8671345';
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max&timezone=auto";

    try {
        $response = Http::get($url);
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Unable to fetch weather data.'], $response->status());
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching weather data.'], 500);
    }
}


    public function fetchCurrentWeather()
    {
        $latitude = '1.8615017';
        $longitude = '103.1095292';
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current_weather=true&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max,weathercode&timezone=auto";

    try {
        $response = Http::get($url);
        if ($response->successful()) {
            $data = $response->json();
            return response()->json([
                'current_weather' => $data['current_weather'] ?? null,
                'forecast' => $data['daily'] ?? null,
            ]);
        } else {
            return response()->json(['error' => 'Unable to fetch weather data.'], $response->status());
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while fetching weather data.'], 500);
    }
    }

    public function fetchForecast()
    {
        $latitude = '1.8615017';
        $longitude = '103.1095292';
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&daily=temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max,weathercode&timezone=auto";

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                return response()->json($response->json()['daily']);
            } else {
                return response()->json(['error' => 'Unable to fetch forecast data.'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching forecast data.'], 500);
        }
    }
}