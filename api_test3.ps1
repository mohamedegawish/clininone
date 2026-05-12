$BASE_URL = "https://clinicone1.com/api"
$ts = Get-Date -Format "yyyyMMddHHmmss"
$regBody = '{"name":"QA3_' + $ts + '","email":"qa3_' + $ts + '@test.com","password":"password123","role":"admin"}'
$headers = @{ "Accept"="application/json"; "Content-Type"="application/json" }

Write-Host "Registering..."
$r = Invoke-WebRequest -Method POST -Uri "$BASE_URL/register" -Headers $headers -Body ([System.Text.Encoding]::UTF8.GetBytes($regBody)) -UseBasicParsing -TimeoutSec 30
$json = $r.Content | ConvertFrom-Json
$TOKEN = $json.token
Write-Host "TOKEN: $TOKEN"

$authH = @{ "Accept"="application/json"; "Authorization"="Bearer $TOKEN" }

$endpoints = @(
    @("GET", "/user"),
    @("GET", "/admin/doctors"),
    @("GET", "/admin/doctors/1"),
    @("GET", "/admin/clinics"),
    @("GET", "/admin/clinics/1"),
    @("GET", "/admin/stats"),
    @("GET", "/clinic/patients"),
    @("GET", "/clinic/patients/1"),
    @("GET", "/clinic/schedules?doctor_id=1"),
    @("GET", "/clinic/appointments"),
    @("GET", "/clinic/appointments/1"),
    @("GET", "/clinic/appointments/available-slots?doctor_id=1&date=2026-05-12&clinic_id=1"),
    @("GET", "/clinic/queue/1")
)

foreach ($ep in $endpoints) {
    $method = $ep[0]
    $path = $ep[1]
    $url = $BASE_URL + $path
    $start = Get-Date
    try {
        $resp = Invoke-WebRequest -Method $method -Uri $url -Headers $authH -UseBasicParsing -TimeoutSec 15
        $ms = [math]::Round(((Get-Date) - $start).TotalMilliseconds)
        $preview = $resp.Content.Substring(0, [Math]::Min(150, $resp.Content.Length))
        Write-Host "OK | $method $path | $($resp.StatusCode) | ${ms}ms | $preview"
    } catch {
        $ms = [math]::Round(((Get-Date) - $start).TotalMilliseconds)
        $code = 0
        $body = ""
        if ($_.Exception.Response) {
            $code = [int]$_.Exception.Response.StatusCode
            try {
                $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $body = $sr.ReadToEnd()
                $sr.Close()
            } catch {}
        }
        $preview = $body.Substring(0, [Math]::Min(200, $body.Length))
        Write-Host "ERR | $method $path | $code | ${ms}ms | $preview"
    }
}

# Now test POST /clinic/appointments
Write-Host "`n--- POST TESTS ---"
$postTests = @(
    @("POST", "/clinic/appointments", '{"doctor_id":1,"patient_id":1,"clinic_id":1,"appointment_date":"2026-05-15","start_time":"10:00","notes":"QA"}'),
    @("POST", "/clinic/schedules", '{"doctor_id":1,"clinic_id":1,"day_of_week":3,"start_time":"09:00","end_time":"17:00","slot_duration":30,"is_active":true}'),
    @("POST", "/clinic/schedules/bulk", '{"doctor_id":1,"clinic_id":1,"schedules":[{"day_of_week":4,"start_time":"09:00","end_time":"14:00","slot_duration":30,"is_active":true}]}'),
    @("PATCH", "/clinic/appointments/1/confirm", '{"clinic_id":1}'),
    @("PATCH", "/clinic/appointments/1/complete", '{"clinic_id":1}'),
    @("PATCH", "/clinic/appointments/1/cancel", '{"clinic_id":1,"cancellation_reason":"test"}'),
    @("PUT", "/clinic/queue/1/advance", '{}')
)

$authHPost = @{ "Accept"="application/json"; "Content-Type"="application/json"; "Authorization"="Bearer $TOKEN" }

foreach ($ep in $postTests) {
    $method = $ep[0]
    $path = $ep[1]
    $bodyData = $ep[2]
    $url = $BASE_URL + $path
    $start = Get-Date
    try {
        $resp = Invoke-WebRequest -Method $method -Uri $url -Headers $authHPost -Body ([System.Text.Encoding]::UTF8.GetBytes($bodyData)) -UseBasicParsing -TimeoutSec 15
        $ms = [math]::Round(((Get-Date) - $start).TotalMilliseconds)
        $preview = $resp.Content.Substring(0, [Math]::Min(200, $resp.Content.Length))
        Write-Host "OK | $method $path | $($resp.StatusCode) | ${ms}ms | $preview"
    } catch {
        $ms = [math]::Round(((Get-Date) - $start).TotalMilliseconds)
        $code = 0
        $body = ""
        if ($_.Exception.Response) {
            $code = [int]$_.Exception.Response.StatusCode
            try {
                $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $body = $sr.ReadToEnd()
                $sr.Close()
            } catch {}
        }
        $preview = $body.Substring(0, [Math]::Min(300, $body.Length))
        Write-Host "ERR | $method $path | $code | ${ms}ms | $preview"
    }
}

# Test POST /public/reviews error details
Write-Host "`n--- BROKEN ENDPOINT DETAILS ---"
$noAuth = @{ "Accept"="application/json"; "Content-Type"="application/json" }
try {
    $resp = Invoke-WebRequest -Method POST -Uri "$BASE_URL/public/reviews" -Headers $noAuth -Body ([System.Text.Encoding]::UTF8.GetBytes('{"doctor_id":1,"rating":5,"reviewer_name":"QA","phone":"01000000000","comment":"test"}')) -UseBasicParsing -TimeoutSec 15
    Write-Host "OK | POST /public/reviews | $($resp.StatusCode) | $($resp.Content.Substring(0,[Math]::Min(300,$resp.Content.Length)))"
} catch {
    $code = 0; $body = ""
    if ($_.Exception.Response) {
        $code = [int]$_.Exception.Response.StatusCode
        try { $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream()); $body = $sr.ReadToEnd(); $sr.Close() } catch {}
    }
    Write-Host "ERR | POST /public/reviews | $code | $($body.Substring(0,[Math]::Min(500,$body.Length)))"
}

# Test refresh-token error details  
try {
    $resp = Invoke-WebRequest -Method POST -Uri "$BASE_URL/refresh-token" -Headers $noAuth -Body ([System.Text.Encoding]::UTF8.GetBytes('{"refresh_token":"invalid_token_here"}')) -UseBasicParsing -TimeoutSec 15
    Write-Host "OK | POST /refresh-token | $($resp.StatusCode) | $($resp.Content.Substring(0,[Math]::Min(300,$resp.Content.Length)))"
} catch {
    $code = 0; $body = ""
    if ($_.Exception.Response) {
        $code = [int]$_.Exception.Response.StatusCode
        try { $sr = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream()); $body = $sr.ReadToEnd(); $sr.Close() } catch {}
    }
    Write-Host "ERR | POST /refresh-token | $code | $($body.Substring(0,[Math]::Min(500,$body.Length)))"
}

Write-Host "`n=== DONE ==="
