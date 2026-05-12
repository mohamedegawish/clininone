<?php

use App\Models\User;
use App\Models\core\Clinic;
use App\Models\core\Doctor;
use App\Models\core\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->clinic = Clinic::factory()->create();
    $this->user = User::factory()->create(['role' => 'doctor']);
    $this->doctor = Doctor::factory()->create([
        'user_id' => $this->user->id,
    ]);
    
    // Associate doctor with clinic
    $this->doctor->clinics()->attach($this->clinic->id, ['role' => 'admin']);
    
    $this->actingAs($this->user, 'sanctum');
});

it('can list patients in the clinic', function () {
    Patient::factory()->count(5)->create(['clinic_id' => $this->clinic->id]);
    
    $response = $this->getJson('/api/clinic/patients');

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonCount(5, 'data.patients');
});

it('cannot see patients from other clinics', function () {
    $otherClinic = Clinic::factory()->create();
    Patient::factory()->count(3)->create(['clinic_id' => $otherClinic->id]);
    
    $response = $this->getJson('/api/clinic/patients');

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonCount(0, 'data.patients');
});

it('can create a patient', function () {
    $patientData = [
        'full_name' => 'John Doe',
        'english_name' => 'John',
        'email' => 'john@example.com',
        'gender' => 'male',
        'phone' => '123456789',
        'address' => '123 Street',
        'birth_date' => '1990-01-01',
        'status' => 'active',
    ];

    $response = $this->postJson('/api/clinic/patients', $patientData);

    $response->assertStatus(201)
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.full_name', 'John Doe');

    $this->assertDatabaseHas('patients', [
        'full_name' => 'John Doe',
        'clinic_id' => $this->clinic->id,
    ]);
});

it('prevents duplicate email in the same clinic', function () {
    Patient::factory()->create([
        'email' => 'duplicate@example.com',
        'clinic_id' => $this->clinic->id
    ]);

    $patientData = [
        'full_name' => 'Jane Doe',
        'english_name' => 'Jane',
        'email' => 'duplicate@example.com',
        'gender' => 'female',
        'phone' => '987654321',
        'address' => '456 Avenue',
        'birth_date' => '1995-05-05',
    ];

    $response = $this->postJson('/api/clinic/patients', $patientData);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can show a patient', function () {
    $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);

    $response = $this->getJson("/api/clinic/patients/{$patient->id}");

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.id', $patient->id);
});

it('can update a patient partially', function () {
    $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id, 'full_name' => 'Old Name']);

    $response = $this->patchJson("/api/clinic/patients/{$patient->id}", [
        'full_name' => 'New Name'
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', true)
        ->assertJsonPath('data.full_name', 'New Name');

    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'full_name' => 'New Name',
    ]);
});

it('can soft delete a patient', function () {
    $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);

    $response = $this->deleteJson("/api/clinic/patients/{$patient->id}");

    $response->assertStatus(200)
        ->assertJsonPath('status', true);

    $this->assertSoftDeleted('patients', ['id' => $patient->id]);
});
