@extends('layouts.app')

@section('title', __('clinic.queue.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.queue.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.queue.subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clinic.appointments.index') }}" class="btn btn-outline">
            <i class="ph-bold ph-calendar"></i>
            <span>{{ __('clinic.queue.appointments_today') }}</span>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Current Patient -->
    <div class="card" style="background: linear-gradient(135deg, var(--clr-primary-600), var(--clr-primary-800)); color: var(--clr-n-0); border: none; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; min-height: 400px;">
        <div class="card-body text-center" style="position: relative; z-index: 1;">
            <div class="mb-16 d-inline-flex align-center gap-8" style="background: rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 20px;">
                <span style="display: block; width: 8px; height: 8px; background: var(--clr-accent-400); border-radius: 50%; box-shadow: 0 0 10px var(--clr-accent-400);"></span>
                <span class="text-sm fw-600" style="letter-spacing: 1px;">{{ __('clinic.queue.current_patient') }}</span>
            </div>
            <div id="current-queue-number" style="font-size: 100px; font-weight: 800; line-height: 1; margin-bottom: 24px; color: var(--clr-accent-400);">--</div>
            <div id="current-patient-name" style="font-size: 24px; font-weight: 700; margin-bottom: 32px;">{{ __('clinic.queue.waiting_first') }}</div>
            <a href="#" id="start-consultation-btn" class="btn btn-lg" style="background: var(--clr-n-0); color: var(--clr-primary-800); width: 80%; max-width: 300px; margin: 0 auto; display: none; font-size: 16px; font-weight: 700;">
                <i class="ph-bold ph-stethoscope"></i>
                <span>{{ __('clinic.queue.start_btn') }}</span>
            </a>
        </div>
    </div>

    <!-- Next Patients List -->
    <div class="card" style="display: flex; flex-direction: column; min-height: 400px;">
        <div class="card-header">
            <h3 class="card-title d-flex align-center gap-8">
                <i class="ph-bold ph-users"></i>
                {{ __('clinic.queue.next_list') }}
            </h3>
            <span class="badge badge-accent dot" id="queue-count">0 {{ __('clinic.queue.patients_count') }}</span>
        </div>
        <div class="card-body" style="padding: 0; flex: 1; overflow-y: auto;">
            <ul style="list-style: none; margin: 0; padding: 0;" id="next-patients-list">
                <li style="padding: 32px; text-align: center; color: var(--clr-n-400);">
                    <i class="ph-fill ph-spinner-gap" style="font-size: 32px; margin-bottom: 12px; display: block; animation: spin 1s linear infinite;"></i>
                    {{ __('clinic.queue.loading') }}
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
.queue-list-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--clr-n-100); transition: background 0.2s; }
.queue-list-item:hover { background: var(--clr-n-50); }
.queue-number-badge { background: var(--clr-primary-50); color: var(--clr-primary-600); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 800; font-size: 16px; border: 1px solid var(--clr-primary-100); }
</style>
@endsection

@push('scripts')
<script>
    const _consultationCreateUrl = '{{ route('clinic.consultations.create', ['appointment' => '__ID__']) }}';

    const labels = {
        nextDirect: "{{ __('clinic.queue.next_direct') }}",
        waiting: "{{ __('clinic.queue.waiting') }}",
        next: "{{ __('clinic.queue.next_direct') }}",
        empty: "{{ __('clinic.queue.empty') }}",
        emptySub: "{{ __('clinic.queue.empty_sub') }}",
        patients: "{{ __('clinic.queue.patients_count') }}"
    };

    function updateQueue() {
        fetch('{{ route('clinic.queue.data') }}')
            .then(r => r.json())
            .then(data => {
                const numEl = document.getElementById('current-queue-number');
                const nameEl = document.getElementById('current-patient-name');
                const btn = document.getElementById('start-consultation-btn');

                if (data.current) {
                    numEl.textContent = String(data.current.queue_number).padStart(3, '0');
                    nameEl.textContent = data.current.name;
                    btn.style.display = 'inline-flex';
                    btn.href = _consultationCreateUrl.replace('__ID__', data.current.id);
                } else {
                    numEl.textContent = '--';
                    nameEl.textContent = "{{ __('clinic.queue.waiting_first') }}";
                    btn.style.display = 'none';
                }

                const list = document.getElementById('next-patients-list');
                const countBadge = document.getElementById('queue-count');
                countBadge.textContent = `${data.next.length} ${labels.patients}`;

                if (data.next.length > 0) {
                    list.innerHTML = data.next.map((p, i) => `
                        <li class="queue-list-item">
                            <div class="d-flex align-center gap-16">
                                <div class="queue-number-badge">${String(p.queue_number).padStart(3,'0')}</div>
                                <div>
                                    <span class="fw-700 d-block" style="color: var(--clr-n-800);">${p.name}</span>
                                    <span class="text-sm" style="color: var(--clr-n-400);">${i === 0 ? `<span style="color:var(--clr-warning);">${labels.nextDirect}</span>` : labels.waiting}</span>
                                </div>
                            </div>
                            <span class="badge ${i === 0 ? 'badge-warning' : 'badge-neutral'} dot">${i === 0 ? labels.next : labels.waiting}</span>
                        </li>`).join('');
                } else {
                    list.innerHTML = `
                        <li style="padding: 48px 24px; text-align: center; color: var(--clr-n-400);">
                            <div style="width: 64px; height: 64px; background: var(--clr-n-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <i class="ph-fill ph-coffee" style="font-size: 28px; color: var(--clr-n-300);"></i>
                            </div>
                            <span class="fw-600" style="display: block; margin-bottom: 4px;">${labels.empty}</span>
                            <span class="text-sm">${labels.emptySub}</span>
                        </li>`;
                }
            })
            .catch(e => console.error(e));
    }

    document.addEventListener('DOMContentLoaded', () => { updateQueue(); setInterval(updateQueue, 5000); });
</script>
@endpush
