<?php

namespace Tests\Feature\Api;

use App\Models\core\Clinic;
use App\Models\core\Doctor;
use App\Models\core\DoctorSchedule;
use App\Models\core\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBookingTest extends TestCase
{
    use RefreshDatabase;

    private Doctor $doctor;
    private Clinic $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::factory()->create();
        $this->doctor = Doctor::factory()->create();
        $this->doctor->clinics()->attach($this->clinic->id);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY);
        
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'clinic_id' => $this->clinic->id,
            'day_of_week' => 1, // Monday
            'start_time' => '10:00:00',
            'end_time' => '14:00:00',
            'slot_duration' => 30,
        ]);
    }

    public function test_it_can_get_available_slots()
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $response = $this->getJson("/api/public/appointments/available-slots?doctor_id={$this->doctor->id}&clinic_id={$this->clinic->id}&date={$nextMonday}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.total_slots', 8);
    }

    public function test_it_can_book_appointment_and_create_patient()
    {
        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $payload = [
            'clinic_id' => $this->clinic->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => $nextMonday,
            'start_time' => '10:00',
            'phone' => '01012345678',
            'full_name' => 'John Doe',
        ];

        $response = $this->postJson("/api/public/appointments/book", $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.queue_number', 1)
                 ->assertJsonPath('data.patient.full_name', 'John Doe');

        $this->assertDatabaseHas('patients', [
            'phone' => '01012345678',
            'full_name' => 'John Doe',
        ]);
        
        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $this->doctor->id,
            'queue_number' => 1,
            'start_time' => '10:00',
        ]);
    }

    public function test_it_can_book_appointment_for_existing_patient()
    {
        $patient = Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'phone' => '01122334455',
            'full_name' => 'Jane Smith'
        ]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

        $payload = [
            'clinic_id' => $this->clinic->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => $nextMonday,
            'start_time' => '11:00',
            'phone' => '01122334455',
            'full_name' => 'Jane Smith (Ignored)',
        ];

        $response = $this->postJson("/api/public/appointments/book", $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.patient.id', $patient->id)
                 ->assertJsonPath('data.queue_number', 1);

        $this->assertEquals(1, Patient::count());
    }
}
