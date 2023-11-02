<?php

namespace App\Interfaces;

Interface SystemDefaultInterface{
    public function updateSystemSettings(array $newDetails);
    public function getSystemSettings();
    public function getActiveTermi();
    public function getActiveZepto();
    public function getActiveSimpleSMS();
}
