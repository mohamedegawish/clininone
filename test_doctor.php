<?php

use App\Models\User;
use App\Models\core\Doctor;
use App\Models\core\Clinic;

$user = User::create([
    'name' => 'Test Dr',
    'email' => 'testdr@example.com',
    'password' => bcrypt('password'),
    'role' => 'doctor'
]);

$clinic = Clinic::first();

$doctor = Doctor::create([
    'user_id' => $user->id,
    'name' => 'Test Dr',
    'email' => 'testdr@example.com',
    'phone' => '1234567890',
    'specialty' => 'Test',
    'price' => 100,
    'status' => 'active'
]);

$doctor->clinics()->attach($clinic->id, ['role' => 'doctor']);

echo "Success\n";
