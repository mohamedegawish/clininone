# ============================================================
# ClinicOne API - Comprehensive Live Test Script
# ============================================================

$BASE_URL = "https://clinicone1.com/api"
$RESULTS = @()
$TOKEN = ""

function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Url,
        [string]$Name,
        [string]$Body = $null,
        [hashtable]$Headers = @{},
        [int]$ExpectedStatus = 200
    )
    
    $defaultHeaders = @{
        "Accept" = "application/json"
        "Content-Type" = "application/json"
    }
    
    foreach ($key in $Headers.Keys) {
        $defaultHeaders[$key] = $Headers[$key]
    }
    
    $params = @{
        Method = $Method
        Uri = $Url
        Headers = $defaultHeaders
        UseBasicParsing = $true
        TimeoutSec = 30
        ErrorAction = "Stop"
    }
    
    if ($Body -and $Method -ne "GET") {
        $params["Body"] = [System.Text.Encoding]::UTF8.GetBytes($Body)
    }
    
    $startTime = Get-Date
    try {
        $response = Invoke-WebRequest @params
        $elapsed = ((Get-Date) - $startTime).TotalMilliseconds
        $statusCode = $response.StatusCode
        $content = $response.Content
        $contentType = $response.Headers["Content-Type"]
        
        # Check if response is JSON
        $isJson = $false
        $jsonData = $null
        try {
            $jsonData = $content | ConvertFrom-Json
            $isJson = $true
        } catch {}
        
        $isHtml = $content -match "<!DOCTYPE|<html"
        
        return @{
            Name = $Name
            Method = $Method
            Url = $Url
            StatusCode = $statusCode
            IsJson = $isJson
            IsHtml = $isHtml
            ContentType = $contentType
            Body = $content
            JsonData = $jsonData
            ElapsedMs = [math]::Round($elapsed, 0)
            Error = $null
        }
    } catch {
        $elapsed = ((Get-Date) - $startTime).TotalMilliseconds
        $statusCode = 0
        $errorBody = ""
        $isJson = $false
        $jsonData = $null
        
        if ($_.Exception.Response) {
            $statusCode = [int]$_.Exception.Response.StatusCode
            try {
                $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
                $errorBody = $reader.ReadToEnd()
                $reader.Close()
            } catch {
                $errorBody = $_.Exception.Message
            }
            try {
                $jsonData = $errorBody | ConvertFrom-Json
                $isJson = $true
            } catch {}
        } else {
            $errorBody = $_.Exception.Message
        }
        
        $isHtml = $errorBody -match "<!DOCTYPE|<html"
        
        return @{
            Name = $Name
            Method = $Method
            Url = $Url
            StatusCode = $statusCode
            IsJson = $isJson
            IsHtml = $isHtml
            ContentType = ""
            Body = $errorBody
            JsonData = $jsonData
            ElapsedMs = [math]::Round($elapsed, 0)
            Error = $_.Exception.Message
        }
    }
}

Write-Host "`n============================================" -ForegroundColor Cyan
Write-Host "  CLINICONE API - LIVE PRODUCTION TEST" -ForegroundColor Cyan
Write-Host "  Base URL: $BASE_URL" -ForegroundColor Cyan
Write-Host "  Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" -ForegroundColor Cyan
Write-Host "============================================`n" -ForegroundColor Cyan

# ============================================================
# 1. PUBLIC ENDPOINTS (No Auth Required)
# ============================================================
Write-Host "=== SECTION 1: PUBLIC ENDPOINTS ===" -ForegroundColor Yellow

$publicTests = @(
    @{ Method="GET"; Path="/public/doctors"; Name="Public - List Doctors" },
    @{ Method="GET"; Path="/public/doctors/1"; Name="Public - Show Doctor" },
    @{ Method="GET"; Path="/public/specialties"; Name="Public - Specialties" },
    @{ Method="GET"; Path="/public/locations"; Name="Public - Locations" },
    @{ Method="GET"; Path="/public/reviews"; Name="Public - List Reviews" },
    @{ Method="GET"; Path="/public/blood-bank/donors"; Name="Public - Blood Bank Donors" },
    @{ Method="GET"; Path="/public/blood-bank/requests"; Name="Public - Blood Bank Requests" },
    @{ Method="GET"; Path="/public/blood-bank/hospitals"; Name="Public - Blood Bank Hospitals" },
    @{ Method="GET"; Path="/public/appointments/available-slots?doctor_id=1&date=2026-05-10&clinic_id=1"; Name="Public - Available Slots" }
)

foreach ($test in $publicTests) {
    $result = Test-Endpoint -Method $test.Method -Url "$BASE_URL$($test.Path)" -Name $test.Name
    $status = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "PASS" } 
              elseif ($result.StatusCode -eq 404) { "404" }
              elseif ($result.StatusCode -eq 500) { "500" }
              else { "FAIL" }
    $icon = if ($status -eq "PASS") { "[PASS]" } elseif ($status -eq "404") { "[404]" } elseif ($status -eq "500") { "[500]" } else { "[FAIL]" }
    $color = if ($status -eq "PASS") { "Green" } elseif ($status -eq "404") { "Yellow" } else { "Red" }
    
    Write-Host "$icon $($test.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | HTML=$($result.IsHtml) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
    $RESULTS += $result
}

# ============================================================
# 2. AUTH TESTS
# ============================================================
Write-Host "`n=== SECTION 2: AUTHENTICATION ===" -ForegroundColor Yellow

# Test 2a: Login with WRONG credentials
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/login" -Name "Auth - Login (WRONG credentials)" -Body '{"email":"wrong@test.com","password":"wrongpass"}'
$icon = if ($result.StatusCode -eq 401 -or $result.StatusCode -eq 422) { "[PASS]" } else { "[FAIL]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Red" }
Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $result

# Test 2b: Login with MISSING fields
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/login" -Name "Auth - Login (MISSING fields)" -Body '{}'
$icon = if ($result.StatusCode -eq 422) { "[PASS]" } else { "[FAIL]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Red" }
Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $result

# Test 2c: Register endpoint reachability
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/register" -Name "Auth - Register (reachability)" -Body '{"name":"TestQA","email":"qa_test_9999@test.com","password":"password123","role":"admin"}'
Write-Host "[INFO] Auth - Register | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor Cyan
$RESULTS += $result

# Test 2d: Forgot Password reachability
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/forgot-password" -Name "Auth - Forgot Password" -Body '{"email":"nonexistent@test.com"}'
Write-Host "[INFO] Auth - Forgot Password | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor Cyan
$RESULTS += $result

# Test 2e: Reset Password reachability
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/reset-password" -Name "Auth - Reset Password" -Body '{"email":"test@test.com","otp":"000000","password":"newpass123"}'
Write-Host "[INFO] Auth - Reset Password | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor Cyan
$RESULTS += $result

# Test 2f: Refresh Token reachability
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/refresh-token" -Name "Auth - Refresh Token" -Body '{"refresh_token":"invalid_token"}'
Write-Host "[INFO] Auth - Refresh Token | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor Cyan
$RESULTS += $result

# Test 2g: Real Login (using seeded credentials from Postman collection)
$loginBody = '{"email":"mahmoud.ali@example.com","password":"password123"}'
$loginResult = Test-Endpoint -Method "POST" -Url "$BASE_URL/login" -Name "Auth - Login (REAL credentials)" -Body $loginBody
$icon = if ($loginResult.StatusCode -eq 200) { "[PASS]" } else { "[FAIL]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Red" }
Write-Host "$icon $($loginResult.Name) | HTTP $($loginResult.StatusCode) | JSON=$($loginResult.IsJson) | ${($loginResult.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $loginResult

# Extract token
if ($loginResult.JsonData) {
    if ($loginResult.JsonData.access_token) {
        $TOKEN = $loginResult.JsonData.access_token
    } elseif ($loginResult.JsonData.token) {
        $TOKEN = $loginResult.JsonData.token
    } elseif ($loginResult.JsonData.data -and $loginResult.JsonData.data.token) {
        $TOKEN = $loginResult.JsonData.data.token
    }
}

Write-Host "`nToken extracted: $(if ($TOKEN) { 'YES (' + $TOKEN.Substring(0, [Math]::Min(20, $TOKEN.Length)) + '...)' } else { 'NO - will try alternate credentials' })" -ForegroundColor $(if ($TOKEN) { "Green" } else { "Red" })

# If first login failed, try admin@clinic.com
if (-not $TOKEN) {
    $loginBody2 = '{"email":"admin@clinic.com","password":"password123"}'
    $loginResult2 = Test-Endpoint -Method "POST" -Url "$BASE_URL/login" -Name "Auth - Login (admin@clinic.com)" -Body $loginBody2
    Write-Host "[INFO] Auth - Login (admin@clinic.com) | HTTP $($loginResult2.StatusCode) | JSON=$($loginResult2.IsJson) | ${($loginResult2.ElapsedMs)}ms" -ForegroundColor Cyan
    $RESULTS += $loginResult2
    
    if ($loginResult2.JsonData) {
        if ($loginResult2.JsonData.access_token) {
            $TOKEN = $loginResult2.JsonData.access_token
        } elseif ($loginResult2.JsonData.token) {
            $TOKEN = $loginResult2.JsonData.token
        } elseif ($loginResult2.JsonData.data -and $loginResult2.JsonData.data.token) {
            $TOKEN = $loginResult2.JsonData.data.token
        }
    }
    Write-Host "Token after 2nd attempt: $(if ($TOKEN) { 'YES' } else { 'NO' })" -ForegroundColor $(if ($TOKEN) { "Green" } else { "Red" })
}

# ============================================================
# 3. PROTECTED ENDPOINTS - NO TOKEN (should be 401)
# ============================================================
Write-Host "`n=== SECTION 3: AUTH GUARD (no token = 401?) ===" -ForegroundColor Yellow

$guardTests = @(
    @{ Method="GET"; Path="/user"; Name="Guard - /user" },
    @{ Method="GET"; Path="/admin/doctors"; Name="Guard - /admin/doctors" },
    @{ Method="GET"; Path="/admin/clinics"; Name="Guard - /admin/clinics" },
    @{ Method="GET"; Path="/clinic/patients"; Name="Guard - /clinic/patients" },
    @{ Method="GET"; Path="/clinic/appointments"; Name="Guard - /clinic/appointments" }
)

foreach ($test in $guardTests) {
    $result = Test-Endpoint -Method $test.Method -Url "$BASE_URL$($test.Path)" -Name $test.Name
    $icon = if ($result.StatusCode -eq 401) { "[PASS]" } elseif ($result.IsHtml) { "[WARN]" } else { "[FAIL]" }
    $color = if ($icon -eq "[PASS]") { "Green" } elseif ($icon -eq "[WARN]") { "Yellow" } else { "Red" }
    $extra = ""
    if ($result.IsHtml) { $extra = " | RETURNS HTML (redirect?)" }
    Write-Host "$icon $($test.Name) | HTTP $($result.StatusCode)$extra | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
    $RESULTS += $result
}

# ============================================================
# 4. PROTECTED ENDPOINTS - WITH TOKEN
# ============================================================
if ($TOKEN) {
    Write-Host "`n=== SECTION 4: PROTECTED ENDPOINTS (with token) ===" -ForegroundColor Yellow
    $authHeaders = @{ "Authorization" = "Bearer $TOKEN" }
    
    $protectedTests = @(
        @{ Method="GET"; Path="/user"; Name="Protected - Get User" },
        @{ Method="GET"; Path="/admin/doctors"; Name="Protected - Admin List Doctors" },
        @{ Method="GET"; Path="/admin/doctors/1"; Name="Protected - Admin Show Doctor #1" },
        @{ Method="GET"; Path="/admin/clinics"; Name="Protected - Admin List Clinics" },
        @{ Method="GET"; Path="/admin/clinics/1"; Name="Protected - Admin Show Clinic #1" },
        @{ Method="GET"; Path="/admin/stats"; Name="Protected - Admin Stats/KPIs" },
        @{ Method="GET"; Path="/clinic/patients"; Name="Protected - Clinic List Patients" },
        @{ Method="GET"; Path="/clinic/patients/1"; Name="Protected - Clinic Show Patient #1" },
        @{ Method="GET"; Path="/clinic/schedules?doctor_id=1"; Name="Protected - Clinic Schedules" },
        @{ Method="GET"; Path="/clinic/appointments"; Name="Protected - Clinic List Appointments" },
        @{ Method="GET"; Path="/clinic/appointments/available-slots?doctor_id=1&date=2026-05-10&clinic_id=1"; Name="Protected - Clinic Available Slots" },
        @{ Method="GET"; Path="/clinic/appointments/1"; Name="Protected - Clinic Show Appointment #1" },
        @{ Method="GET"; Path="/clinic/queue/1"; Name="Protected - Clinic Queue" }
    )
    
    foreach ($test in $protectedTests) {
        $result = Test-Endpoint -Method $test.Method -Url "$BASE_URL$($test.Path)" -Name $test.Name -Headers $authHeaders
        $status = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "PASS" }
                  elseif ($result.StatusCode -eq 403) { "FORBIDDEN" }
                  elseif ($result.StatusCode -eq 404) { "404" }
                  elseif ($result.StatusCode -eq 500) { "500" }
                  else { "FAIL" }
        $icon = if ($status -eq "PASS") { "[PASS]" } elseif ($status -eq "FORBIDDEN") { "[403]" } elseif ($status -eq "404") { "[404]" } elseif ($status -eq "500") { "[500]" } else { "[FAIL]" }
        $color = if ($status -eq "PASS") { "Green" } elseif ($status -eq "FORBIDDEN" -or $status -eq "404") { "Yellow" } else { "Red" }
        $extra = ""
        if ($result.IsHtml) { $extra = " | HTML!" }
        Write-Host "$icon $($test.Name) | HTTP $($result.StatusCode)$extra | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
        $RESULTS += $result
    }
    
    # Test Logout
    $result = Test-Endpoint -Method "POST" -Url "$BASE_URL/logout" -Name "Protected - Logout" -Headers $authHeaders
    $icon = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "[PASS]" } else { "[FAIL]" }
    $color = if ($icon -eq "[PASS]") { "Green" } else { "Red" }
    Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
    $RESULTS += $result
} else {
    Write-Host "`n=== SECTION 4: SKIPPED (no valid token) ===" -ForegroundColor Red
}

# ============================================================
# 5. PUBLIC POST ENDPOINTS
# ============================================================
Write-Host "`n=== SECTION 5: PUBLIC POST ENDPOINTS ===" -ForegroundColor Yellow

# Public Review submission
$reviewBody = '{"doctor_id":1,"rating":5,"reviewer_name":"QA Tester","phone":"01000000000","comment":"Great doctor"}'
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/public/reviews" -Name "Public - Submit Review"  -Body $reviewBody
$icon = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "[PASS]" } else { "[INFO]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Cyan" }
Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $result

# Public Blood Bank - Store Donor
$donorBody = '{"name":"QA Donor","phone":"01111111111","blood_type":"A+","age":30,"governorate":"Cairo","city":"Nasr City"}'
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/public/blood-bank/donors" -Name "Public - Register Blood Donor" -Body $donorBody
$icon = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "[PASS]" } else { "[INFO]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Cyan" }
Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $result

# Public Blood Bank - Store Request
$requestBody = '{"patient_name":"QA Patient","phone":"01222222222","blood_type":"O-","hospital":"Cairo Hospital","urgency":"normal"}'
$result = Test-Endpoint -Method "POST" -Url "$BASE_URL/public/blood-bank/requests" -Name "Public - Blood Bank Request" -Body $requestBody
$icon = if ($result.StatusCode -ge 200 -and $result.StatusCode -lt 300) { "[PASS]" } else { "[INFO]" }
$color = if ($icon -eq "[PASS]") { "Green" } else { "Cyan" }
Write-Host "$icon $($result.Name) | HTTP $($result.StatusCode) | JSON=$($result.IsJson) | ${($result.ElapsedMs)}ms" -ForegroundColor $color
$RESULTS += $result

# ============================================================
# 6. SUMMARY
# ============================================================
Write-Host "`n============================================" -ForegroundColor Cyan
Write-Host "  SUMMARY" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan

$totalTests = $RESULTS.Count
$passed = ($RESULTS | Where-Object { $_.StatusCode -ge 200 -and $_.StatusCode -lt 300 }).Count
$notFound = ($RESULTS | Where-Object { $_.StatusCode -eq 404 }).Count
$serverError = ($RESULTS | Where-Object { $_.StatusCode -eq 500 }).Count
$unauthorized = ($RESULTS | Where-Object { $_.StatusCode -eq 401 }).Count
$forbidden = ($RESULTS | Where-Object { $_.StatusCode -eq 403 }).Count
$validation = ($RESULTS | Where-Object { $_.StatusCode -eq 422 }).Count
$htmlResponses = ($RESULTS | Where-Object { $_.IsHtml -eq $true }).Count
$connectionErrors = ($RESULTS | Where-Object { $_.StatusCode -eq 0 }).Count
$slowEndpoints = ($RESULTS | Where-Object { $_.ElapsedMs -gt 3000 }).Count

Write-Host "Total Tests:      $totalTests"
Write-Host "2xx Success:      $passed" -ForegroundColor Green
Write-Host "401 Unauthorized: $unauthorized" -ForegroundColor Yellow
Write-Host "403 Forbidden:    $forbidden" -ForegroundColor Yellow
Write-Host "404 Not Found:    $notFound" -ForegroundColor Yellow
Write-Host "422 Validation:   $validation" -ForegroundColor Yellow
Write-Host "500 Server Error: $serverError" -ForegroundColor Red
Write-Host "Connection Error: $connectionErrors" -ForegroundColor Red
Write-Host "HTML Responses:   $htmlResponses" -ForegroundColor $(if ($htmlResponses -gt 0) { "Red" } else { "Green" })
Write-Host "Slow (>3s):       $slowEndpoints" -ForegroundColor $(if ($slowEndpoints -gt 0) { "Yellow" } else { "Green" })

Write-Host "`n=== DETAILED RESULTS ===" -ForegroundColor Cyan
foreach ($r in $RESULTS) {
    $icon = if ($r.StatusCode -ge 200 -and $r.StatusCode -lt 300) { "PASS" }
            elseif ($r.StatusCode -eq 401 -or $r.StatusCode -eq 422) { "EXPECTED" }
            elseif ($r.StatusCode -eq 404) { "NOT_FOUND" }
            elseif ($r.StatusCode -eq 500) { "SERVER_ERR" }
            elseif ($r.StatusCode -eq 429) { "THROTTLED" }
            elseif ($r.StatusCode -eq 0) { "CONN_ERR" }
            else { "ISSUE" }
    
    $bodyPreview = ""
    if ($r.Body) {
        $bodyPreview = $r.Body.Substring(0, [Math]::Min(120, $r.Body.Length)) -replace "`n", " " -replace "`r", ""
    }
    
    Write-Host "[$icon] $($r.Method) $($r.Url) => $($r.StatusCode) | ${($r.ElapsedMs)}ms | $bodyPreview"
}

# Output JSON results
$jsonOutput = $RESULTS | ForEach-Object {
    @{
        name = $_.Name
        method = $_.Method
        url = $_.Url
        status_code = $_.StatusCode
        is_json = $_.IsJson
        is_html = $_.IsHtml
        elapsed_ms = $_.ElapsedMs
        body_preview = if ($_.Body) { $_.Body.Substring(0, [Math]::Min(200, $_.Body.Length)) } else { "" }
        error = $_.Error
    }
} | ConvertTo-Json -Depth 5

$jsonOutput | Out-File -FilePath "c:\Users\Acer Nitro V\Downloads\clinic\web\api_test_results.json" -Encoding UTF8

Write-Host "`nResults saved to api_test_results.json" -ForegroundColor Green
