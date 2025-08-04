# Hermes API Testing Script for PowerShell
# Run this in VS Code PowerShell terminal on Windows 11

# Configuration
$baseUrl = "http://localhost:8000"  # Change to your Laravel app URL
$apiKey = "lPpj5DMU2xPCyNfmg5cC46NyULFZbj7vO3xQisD0ryexuJNgpsz9Yk9t2eSteeiC"
$apiSecret = "iEazbo63WgX51ckBcmzhl957XyCAscSAcXrabgWzUUETjRpnJ7qgZo5KxBjv0m7A"  # Optional but recommended

# Test 1: Send Simple SMS
Write-Host "Test 1: Sending Simple SMS..." -ForegroundColor Yellow

$headers = @{
    "X-API-Key" = $apiKey
    "X-API-Secret" = $apiSecret
    "Content-Type" = "application/json"
    "Accept" = "application/json"
}

$body = @{
    message_text = "Hello World"
    sender = "Hermes 1"
    recipient_phone_number = "+35699829840"
    reference = "test-001"
    allow_multi_part = $false
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/v1/sms/send" `
                                 -Method Post `
                                 -Headers $headers `
                                 -Body $body `
                                 -ContentType "application/json"
    
    Write-Host "Success! Message sent." -ForegroundColor Green
    Write-Host "Status: $($response.status_message)"
    Write-Host "Status Code: $($response.status_code)"
    Write-Host "Accepted Count: $($response.result.accepted_count)"
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10
}
catch {
    Write-Host "Error sending SMS:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $reader.DiscardBufferedData()
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody
    }
}

Write-Host "`n----------------------------`n" -ForegroundColor Gray

# Test 2: Send SMS to Multiple Recipients
Write-Host "Test 2: Sending SMS to Multiple Recipients..." -ForegroundColor Yellow

$bodyMultiple = @{
    message_text = "Hello Multiple Recipients"
    sender = "Hermes 2"
    recipient_phone_number = @("+35699829840", "+35699986555", "+35699829840")
    reference = "test-002"
    allow_multi_part = $true
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/v1/sms/send" `
                                 -Method Post `
                                 -Headers $headers `
                                 -Body $bodyMultiple `
                                 -ContentType "application/json"
    
    Write-Host "Success! Messages sent to multiple recipients." -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10
}
catch {
    Write-Host "Error:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n----------------------------`n" -ForegroundColor Gray

# Test 3: Test Multi-part Message Validation
Write-Host "Test 3: Testing Multi-part Message Validation..." -ForegroundColor Yellow

$longMessage = "This is a very long message that exceeds 160 characters. " +
               "It contains multiple sentences to ensure it goes over the single SMS limit. " +
               "This should trigger the multi-part validation when allow_multi_part is false."

$bodyLong = @{
    message_text = $longMessage
    sender = "Hermes 3"
    recipient_phone_number = "+35699829840"
    reference = "test-003"
    allow_multi_part = $false
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/v1/sms/send" `
                                 -Method Post `
                                 -Headers $headers `
                                 -Body $bodyLong `
                                 -ContentType "application/json"
    
    Write-Host "Message sent (unexpected)" -ForegroundColor Yellow
}
catch {
    Write-Host "Expected error for long message:" -ForegroundColor Green
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $reader.DiscardBufferedData()
        $responseBody = $reader.ReadToEnd()
        Write-Host $responseBody
    }
}

Write-Host "`n----------------------------`n" -ForegroundColor Gray

# Test 4: Send Rich SMS (WhatsApp with Media)
Write-Host "Test 4: Sending Rich SMS with Media..." -ForegroundColor Yellow

$bodyRich = @{
    message_text = "Check out this image!"
    sender = "Hermes 4"
    recipient_phone_number = "+35699829840"
    reference = "test-004"
    channel = "WHATSAPP"
    hybrid_app_key = "your-whatsapp-hybrid-key"  # Replace with actual key
    media = @{
        url = "https://example.com/image.jpg"
        type = "image/jpeg"
        caption = "Beautiful sunset"
    }
} | ConvertTo-Json -Depth 10

try {
    $response = Invoke-RestMethod -Uri "$baseUrl/api/v1/sms/send-rich" `
                                 -Method Post `
                                 -Headers $headers `
                                 -Body $bodyRich `
                                 -ContentType "application/json"
    
    Write-Host "Success! Rich message sent." -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10
}
catch {
    Write-Host "Error sending rich SMS:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $reader.DiscardBufferedData()
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody
    }
}

Write-Host "`n----------------------------`n" -ForegroundColor Gray

# Test 5: Check Message Status
Write-Host "Test 5: Checking Message Status..." -ForegroundColor Yellow

$statusUrl = "$baseUrl/api/v1/sms/status?reference=test-001"

try {
    $response = Invoke-RestMethod -Uri $statusUrl `
                                 -Method Get `
                                 -Headers $headers
    
    Write-Host "Status check successful:" -ForegroundColor Green
    Write-Host "Reference: $($response.reference)"
    Write-Host "Status: $($response.status)"
    Write-Host "Response:" -ForegroundColor Cyan
    $response | ConvertTo-Json -Depth 10
}
catch {
    Write-Host "Error checking status:" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n----------------------------`n" -ForegroundColor Gray

# Function to test with custom parameters
function Test-HermesSMS {
    param(
        [Parameter(Mandatory=$true)]
        [string]$Message,
        
        [Parameter(Mandatory=$true)]
        [string]$RecipientPhone,
        
        [string]$Sender = "Hermes 1",
        [string]$Reference = "ps-test-$(Get-Date -Format 'yyyyMMddHHmmss')",
        [bool]$AllowMultiPart = $true
    )
    
    $headers = @{
        "X-API-Key" = $apiKey
        "X-API-Secret" = $apiSecret
        "Content-Type" = "application/json"
        "Accept" = "application/json"
    }
    
    $body = @{
        message_text = $Message
        sender = $Sender
        recipient_phone_number = $RecipientPhone
        reference = $Reference
        allow_multi_part = $AllowMultiPart
    } | ConvertTo-Json
    
    try {
        $response = Invoke-RestMethod -Uri "$baseUrl/api/v1/sms/send" `
                                     -Method Post `
                                     -Headers $headers `
                                     -Body $body `
                                     -ContentType "application/json"
        
        Write-Host "SMS sent successfully!" -ForegroundColor Green
        return $response
    }
    catch {
        Write-Host "Failed to send SMS:" -ForegroundColor Red
        Write-Host $_.Exception.Message
        return $null
    }
}

# Example usage of the function
Write-Host "Example: Using Test-HermesSMS function..." -ForegroundColor Yellow
# Test-HermesSMS -Message "Test from PowerShell function" -RecipientPhone "+35699829840"

Write-Host "`nAll tests completed!" -ForegroundColor Green

# Alternative using curl if Invoke-RestMethod has issues
Write-Host "`n----------------------------`n" -ForegroundColor Gray
Write-Host "Alternative: Using curl command..." -ForegroundColor Yellow
Write-Host @"
curl -X POST "$baseUrl/api/v1/sms/send" ``
  -H "X-API-Key: $apiKey" ``
  -H "X-API-Secret: $apiSecret" ``
  -H "Content-Type: application/json" ``
  -H "Accept: application/json" ``
  -d '{
    "message_text": "Hello from curl",
    "sender": "test",
    "recipient_phone_number": "+35699829840",
    "reference": "curl-test-001",
    "allow_multi_part": false
  }'
"@ -ForegroundColor Cyan