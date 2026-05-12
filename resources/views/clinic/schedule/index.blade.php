@extends('layouts.app')

@section('title', __('clinic.schedule.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.schedule.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.schedule.subtitle') }}</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('clinic.schedule.store') }}" method="POST">
            @csrf
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('clinic.schedule.day') }}</th>
                            <th>{{ __('clinic.schedule.working') }}</th>
                            <th>{{ __('clinic.schedule.start_time') }}</th>
                            <th>{{ __('clinic.schedule.end_time') }}</th>
                            <th>{{ __('clinic.schedule.slot_duration') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $daysMap = [
                                0 => __('clinic.schedule.sunday'),
                                1 => __('clinic.schedule.monday'),
                                2 => __('clinic.schedule.tuesday'),
                                3 => __('clinic.schedule.wednesday'),
                                4 => __('clinic.schedule.thursday'),
                                5 => __('clinic.schedule.friday'),
                                6 => __('clinic.schedule.saturday'),
                            ];
                        @endphp

                        @foreach($daysMap as $index => $dayName)
                            @php
                                $schedule = $schedules->get($index);
                                $isActive = $schedule ? $schedule->is_active : false;
                            @endphp
                            <tr>
                                <td class="fw-600">
                                    <input type="hidden" name="schedules[{{ $index }}][day_of_week]" value="{{ $index }}">
                                    {{ $dayName }}
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="hidden" name="schedules[{{ $index }}][is_active]" value="0">
                                        <input type="checkbox" name="schedules[{{ $index }}][is_active]" value="1" class="schedule-toggle" data-target="row-{{ $index }}" {{ $isActive ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $index }}][start_time]" id="start-{{ $index }}" class="form-control" value="{{ $schedule ? substr($schedule->start_time, 0, 5) : '09:00' }}" {{ !$isActive ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $index }}][end_time]" id="end-{{ $index }}" class="form-control" value="{{ $schedule ? substr($schedule->end_time, 0, 5) : '17:00' }}" {{ !$isActive ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <input type="number" name="schedules[{{ $index }}][slot_duration]" id="slot-{{ $index }}" class="form-control" value="{{ $schedule ? $schedule->slot_duration : 30 }}" min="5" step="5" {{ !$isActive ? 'disabled' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-24 d-flex justify-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ph-bold ph-floppy-disk"></i>
                    <span>{{ __('clinic.common.save') }} {{ __('clinic.schedule.title') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.switch { position: relative; display: inline-block; width: 40px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--clr-n-300); transition: .4s; border-radius: 24px; }
.slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: var(--clr-primary-600); }
input:checked + .slider:before { transform: translateX(16px); }
</style>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.schedule-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const index = this.dataset.target.split('-')[1];
            const isChecked = this.checked;
            document.getElementById(`start-${index}`).disabled = !isChecked;
            document.getElementById(`end-${index}`).disabled = !isChecked;
            document.getElementById(`slot-${index}`).disabled = !isChecked;
        });
    });
</script>
@endpush
