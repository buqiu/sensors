<?php
namespace Buqiu\Sensors;

use Illuminate\Support\ServiceProvider;
class SensorsProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishesConfiguration();
    }

    private function publishesConfiguration()
    {
        $this->publishes([
            __DIR__."/config/sensors.php" => config_path('sensors.php'),
        ], 'buqiu-sensors-config');
    }
}