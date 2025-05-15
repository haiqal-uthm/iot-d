<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $this->database = (new Factory)
            ->withServiceAccount(config('firebase.credentials.json'))
            ->withDatabaseUri(config('firebase.database.url'))
            ->createDatabase();
    }

    public function getDurianCount()
    {
        return $this->database->getReference('vibrationCount')->getValue();
    }

    public function getDeviceStatus()
    {
        return $this->database->getReference('deviceControl')->getValue();
    }

    //public function getWeatherData()
    //{
    //    return $this->database->getReference('weather_data')->getValue();
    //}

    public function getTotalGarden()
    {
        return $this->database->getReference('total_garden')->getValue();
    }
}
