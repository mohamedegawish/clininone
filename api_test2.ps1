$BASE_URL = "https://clinicone1.com/api"

# Step 1: Register a fresh user to get a token (avoids login throttle)
Write-Host "=== Registering fresh test user ===" -ForegroundColor Cyan
$ts = Get-Date -Format "yyyyMMddHHmmss"
$regBody = @{
    name = "QA_Test_$ts"
    email = "qa_test_${ts}@test.com"
    password = "password123"
    role = "admin"
} | ConvertTo-Json

$TOKEN = ""
try {
    $regResp = Invoke-WebRequest -Method POST -Uri "$BASE_URL/register" `
        -Headers @{ "Accept"="application/json"; "Content-Type"="application/json" } `
        -Body ([System.Text.Encoding]::UTF8.GetBytes($regBody)) `
        -UseBasicParsing -TimeoutSec 30
    $regJson = $regResp.Content | ConvertFrom-Json
    if ($regJson.token) { $TOKEN = $regJson.token }
    elseif ($regJson.access_token) { $TOKEN = $regJson.access_token }
    elseif ($regJson.data -and $regJson.data.token) { $TOKEN = $regJson.data.token }
    Write-Host "Register: HTTP $($regResp.StatusCode)" -ForegroundColor Green
    Write-Host "Token: $($TOKEN.Substring(0, [Math]::Min(40, $TOKEN.Length)))..." -ForegroundColor Green
    Write-Host "Full response: $($regResp.Content)" -ForegroundColor DarkGray
} catch {
    $code = 0
    $errBody = ""
    if ($_.Exception.Response) {
        $code = [int]$_.Exception.Response.StatusCode
        try {
            $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $errBody = $sr.ReadToEnd()
            $sr.Close()
        } catch {}
    }
    Write-Host "Register FAILED: HTTP $code | $errBody" -ForegroundColor Red
}

if (-not $TOKEN) {
    Write-Host "NO TOKEN - Cannot test protected endpoints" -ForegroundColor Red
    exit 1
}

# Step 2: Test all protected endpoints
Write-Host "`n=== PROTECTED ENDPOINT TESTS ===" -ForegroundColor Yellow

$authHeaders = @{
    "Accept" = "application/json"
    "Content-Type" = "application/json"
    "Authorization" = "Bearer $TOKEN"
}

$results = @()

function Do-Test {
    param([string]$Method, [string]$Path, [string]$Name, [string]$Body = "")
    
    $url = "$BASE_URL$Path"
    $startTime = Get-Date
    try {
        $params = @{
            Method = $Method
            Uri = $url
            Headers = $authHeaders
            UseBasicParsing = $true
            TimeoutSec = 20
        }
        if ($Body -and $Method -ne "GET") {
            $params["Body"] = [System.Text.Encoding]::UTF8.GetBytes($Body)
        }
        $r = Invoke-WebRequest @params
        $elapsed = [math]::Round(((Get-Date) - $startTime).TotalMilliseconds)
        $preview = $r.Content.Substring(0, [Math]::Min(180, $r.Content.Length))
        $isHtml = $r.Content -match "<!DOCTYPE|<html"
        Write-Host "[PASS] $Name | $($r.StatusCode) | ${elapsed}ms$(if($isHtml){' | HTML!'})" -ForegroundColor Green
        Write-Host "  -> $preview" -ForegroundColor DarkGray
        return @{ Name=$Name; Status=$r.StatusCode; OK=$true; Elapsed=$elapsed; Body=$preview; IsHtml=$isHtml }
    } catch {
        $elapsed = [math]::Round(((Get-Date) - $startTime).TotalMilliseconds)
        $code = 0
        $errBody = $_.Exception.Message
        if ($_.Exception.Response) {
            $code = [int]$_.Exception.Response.StatusCode
            try {
                $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $errBody = $sr.ReadToEnd()
                $sr.Close()
            } catch {}
        }
        $isHtml = $errBody -match "<!DOCTYPE|<html"
        $preview = $errBody.Substring(0, [Math]::Min(180, $errBody.Length))
        $color = if ($code -eq 401 -or $code -eq 422) { "Yellow" } elseif ($code -eq 403) { "Yellow" } else { "Red" }
        Write-Host "[FAIL] $Name | $code | ${elapsed}ms$(if($isHtml){' | HTML!'})" -ForegroundColor $color
        Write-Host "  -> $preview" -ForegroundColor DarkGray
        return @{ Name=$Name; Status=$code; OK=$false; Elapsed=$elapsed; Body=$preview; IsHtml=$isHtml }
    }
}

# ---- GET Endpoints ----
Write-Host "`n--- Auth ---" -ForegroundColor Cyan
Do-Test "GET" "/user" "GET /user"

Write-Host "`n--- Admin Doctors ---" -ForegroundColor Cyan
Do-Test "GET" "/admin/doctors" "GET /admin/doctors"
Do-Test "GET" "/admin/doctors/1" "GET /admin/doctors/1"

Write-Host "`n--- Admin Clinics ---" -ForegroundColor Cyan
Do-Test "GET" "/admin/clinics" "GET /admin/clinics"
Do-Test "GET" "/admin/clinics/1" "GET /admin/clinics/1"

Write-Host "`n--- Admin Stats ---" -ForegroundColor Cyan
Do-Test "GET" "/admin/stats" "GET /admin/stats"

Write-Host "`n--- Clinic Patients ---" -ForegroundColor Cyan
Do-Test "GET" "/clinic/patients" "GET /clinic/patients"
Do-Test "GET" "/clinic/patients/1" "GET /clinic/patients/1"

Write-Host "`n--- Clinic Schedules ---" -ForegroundColor Cyan
Do-Test "GET" "/clinic/schedules?doctor_id=1" "GET /clinic/schedules"

Write-Host "`n--- Clinic Appointments ---" -ForegroundColor Cyan
Do-Test "GET" "/clinic/appointments" "GET /clinic/appointments"
Do-Test "GET" "/clinic/appointments/available-slots?doctor_id=1&date=2026-05-12&clinic_id=1" "GET /clinic/appointments/available-slots"
Do-Test "GET" "/clinic/appointments/1" "GET /clinic/appointments/1"

Write-Host "`n--- Clinic Queue ---" -ForegroundColor Cyan
Do-Test "GET" "/clinic/queue/1" "GET /clinic/queue/1"

# ---- POST/PUT/PATCH/DELETE Endpoints ----
Write-Host "`n--- Write Operations ---" -ForegroundColor Cyan

# Create a clinic
$clinicBody = '{"name":"QA Test Clinic","address":"Test Address Cairo"}'
Do-Test "POST" "/admin/clinics" "POST /admin/clinics" $clinicBody

# Create a doctor
$doctorBody = '{"name":"Dr. QA Test","email":"dr_qa_' + $ts + '@test.com","password":"password123","phone":"01234567890","specialty":"General","status":"active","gender":"male","governorate":"Cairo","city":"Nasr City","experience_years":3,"qualification":"MD","bio":"QA Test Doctor"}'
Do-Test "POST" "/admin/doctors" "POST /admin/doctors" $doctorBody

# Create a patient
$patientBody = '{"clinic_id":1,"full_name":"QA Patient","english_name":"QA Patient EN","phone":"01099999999","ssn":"29900010000001","birth_date":"1999-01-01","gender":"male","nationality":"Egypt","address":"QA Address"}'
Do-Test "POST" "/clinic/patients" "POST /clinic/patients" $patientBody

# Create a schedule
$schedBody = '{"doctor_id":1,"clinic_id":1,"day_of_week":0,"start_time":"09:00","end_time":"17:00","slot_duration":30,"is_active":true}'
Do-Test "POST" "/clinic/schedules" "POST /clinic/schedules" $schedBody

# Bulk schedules
$bulkBody = '{"doctor_id":1,"clinic_id":1,"schedules":[{"day_of_week":1,"start_time":"09:00","end_time":"14:00","slot_duration":30,"is_active":true}]}'
Do-Test "POST" "/clinic/schedules/bulk" "POST /clinic/schedules/bulk" $bulkBody

# Book appointment
$apptBody = '{"doctor_id":1,"patient_id":1,"clinic_id":1,"appointment_date":"2026-05-15","start_time":"10:00","notes":"QA test"}'
Do-Test "POST" "/clinic/appointments" "POST /clinic/appointments" $apptBody

# Confirm appointment
Do-Test "PATCH" "/clinic/appointments/1/confirm" "PATCH /clinic/appointments/1/confirm" '{"clinic_id":1}'

# Complete appointment
Do-Test "PATCH" "/clinic/appointments/1/complete" "PATCH /clinic/appointments/1/complete" '{"clinic_id":1}'

# Cancel appointment
Do-Test "PATCH" "/clinic/appointments/1/cancel" "PATCH /clinic/appointments/1/cancel" '{"clinic_id":1,"cancellation_reason":"QA test cancel"}'

# Queue operations
Do-Test "PUT" "/clinic/queue/1/advance" "PUT /clinic/queue/1/advance"

# Update doctor
Do-Test "PUT" "/admin/doctors/1" "PUT /admin/doctors/1" '{"name":"Dr. Updated QA","specialty":"Dermatology"}'

# Update clinic
Do-Test "PUT" "/admin/clinics/1" "PUT /admin/clinics/1" '{"name":"Updated QA Clinic","address":"Updated Address"}'

# Update patient
Do-Test "PUT" "/clinic/patients/1" "PUT /clinic/patients/1" '{"clinic_id":1,"full_name":"Updated QA Patient"}'

Write-Host "`n--- Public Endpoints Error Bodies ---" -ForegroundColor Cyan
# Test reviews GET with doctor_id
$noAuthHeaders = @{ "Accept"="application/json"; "Content-Type"="application/json" }
try {
    $r = Invoke-WebRequest -Method GET -Uri "$BASE_URL/public/reviews?doctor_id=1" -Headers $noAuthHeaders -UseBasicParsing -TimeoutSec 15
    Write-Host "[PASS] GET /public/reviews?doctor_id=1 | $($r.StatusCode)" -ForegroundColor Green
    Write-Host "  -> $($r.Content.Substring(0, [Math]::Min(200, $r.Content.Length)))" -ForegroundColor DarkGray
} catch {
    $code = if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
    $body = ""
    if ($_.Exception.Response) {
        try {
            $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $body = $sr.ReadToEnd()
            $sr.Close()
        } catch {}
    }
    Write-Host "[FAIL] GET /public/reviews?doctor_id=1 | $code" -ForegroundColor Red
    Write-Host "  -> $($body.Substring(0, [Math]::Min(300, $body.Length)))" -ForegroundColor DarkGray
}

# Test POST review with proper body
try {
    $reviewBody = '{"doctor_id":1,"rating":5,"reviewer_name":"QA","phone":"01000000000","comment":"test"}'
    $r = Invoke-WebRequest -Method POST -Uri "$BASE_URL/public/reviews" -Headers $noAuthHeaders -Body ([System.Text.Encoding]::UTF8.GetBytes($reviewBody)) -UseBasicParsing -TimeoutSec 15
    Write-Host "[PASS] POST /public/reviews | $($r.StatusCode)" -ForegroundColor Green
    Write-Host "  -> $($r.Content.Substring(0, [Math]::Min(200, $r.Content.Length)))" -ForegroundColor DarkGray
} catch {
    $code = if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
    $body = ""
    if ($_.Exception.Response) {
        try {
            $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $body = $sr.ReadToEnd()
            $sr.Close()
        } catch {}
    }
    Write-Host "[FAIL] POST /public/reviews | $code" -ForegroundColor Red
    Write-Host "  -> $($body.Substring(0, [Math]::Min(300, $body.Length)))" -ForegroundColor DarkGray
}

# Test refresh-token error body
try {
    $r = Invoke-WebRequest -Method POST -Uri "$BASE_URL/refresh-token" -Headers $noAuthHeaders -Body '{"refresh_token":"invalid"}' -UseBasicParsing -TimeoutSec 15
    Write-Host "[PASS] POST /refresh-token | $($r.StatusCode)" -ForegroundColor Green
} catch {
    $code = if ($_.Exception.Response) { [int]$_.Exception.Response.StatusCode } else { 0 }
    $body = ""
    if ($_.Exception.Response) {
        try {
            $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $body = $sr.ReadToEnd()
            $sr.Close()
        } catch {}
    }
    Write-Host "[FAIL] POST /refresh-token | $code" -ForegroundColor Red
    Write-Host "  -> $($body.Substring(0, [Math]::Min(500, $body.Length)))" -ForegroundColor DarkGray
}

# Logout
Write-Host "`n--- Logout ---" -ForegroundColor Cyan
Do-Test "POST" "/logout" "POST /logout"

Write-Host "`n=== ALL TESTS COMPLETE ===" -ForegroundColor Green
