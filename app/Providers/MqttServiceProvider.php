<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Device;

class MqttServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->handleDeviceRegistration();
    }

    protected function handleDeviceRegistration(): void
    {
        MQTT::subscribe('device/register/#', function ($topic, $message) {
            $macAddress = str_replace('device/register/', '', $topic);
            $macAddress = strtoupper($macAddress);
            
            // Validate MAC address format
            if (!preg_match('/^([0-9A-F]{2}:){5}[0-9A-F]{2}$/', $macAddress)) {
                MQTT::publish("device/response/$macAddress", json_encode([
                    'status' => 'error',
                    'message' => 'Invalid MAC address format'
                ]));
                return;
            }

            $device = Device::findByMacAddress($macAddress);
            
            if (!$device) {
                $device = Device::create([
                    'mac_address' => $macAddress,
                    'device_type' => 'esp32',
                    'metadata' => json_decode($message, true) ?? [],
                    'last_seen_at' => now(),
                ]);
            } else {
                $device->updateLastSeen();
                $device->update(['metadata' => json_decode($message, true) ?? []]);
            }

            MQTT::publish("device/response/$macAddress", json_encode([
                'status' => 'success',
                'device_id' => $device->id,
                'topic' => "device/$device->id/data"
            ]));
        });
    }
} 