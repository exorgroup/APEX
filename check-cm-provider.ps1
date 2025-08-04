# PowerShell script to check CMProvider database connection
# Save as check-cm-provider.ps1

Write-Host "Checking CMProvider Database Connection..." -ForegroundColor Yellow
Write-Host "=========================================" -ForegroundColor Yellow

# Check the getApiKey method in CMProvider.php
$providerPath = "Apex\Hermes\src\Providers\CMTelecom\CMProvider.php"

if (Test-Path $providerPath) {
    Write-Host "`nChecking CMProvider.php content..." -ForegroundColor Cyan
    
    # Look for the getApiKey method
    $content = Get-Content $providerPath -Raw
    
    # Check if it's using connection('hermes')
    if ($content -match "DB::connection\('hermes'\)") {
        Write-Host "`n✓ File is using DB::connection('hermes')" -ForegroundColor Green
    } else {
        Write-Host "`n✗ File is NOT using DB::connection('hermes')" -ForegroundColor Red
        
        # Check what it's using instead
        if ($content -match "DB::table\('hermes_api_keys'\)") {
            Write-Host "  It's using: DB::table('hermes_api_keys') without connection" -ForegroundColor Yellow
        }
    }
    
    # Show the actual getApiKey method
    Write-Host "`nLooking for getApiKey method..." -ForegroundColor Cyan
    $lines = Get-Content $providerPath
    $inMethod = $false
    $methodLines = @()
    
    foreach ($line in $lines) {
        if ($line -match "protected function getApiKey") {
            $inMethod = $true
        }
        
        if ($inMethod) {
            $methodLines += $line
            
            # Simple check for end of method (this is approximate)
            if ($line -match "^\s*\}\s*$" -and $methodLines.Count -gt 5) {
                break
            }
        }
    }
    
    if ($methodLines.Count -gt 0) {
        Write-Host "`ngetApiKey method:" -ForegroundColor Green
        $methodLines | ForEach-Object { Write-Host $_ }
    }
    
} else {
    Write-Host "CMProvider.php not found at expected path!" -ForegroundColor Red
    Write-Host "Looking for it..." -ForegroundColor Yellow
    Get-ChildItem -Path "." -Filter "CMProvider.php" -Recurse -ErrorAction SilentlyContinue | ForEach-Object {
        Write-Host "Found at: $($_.FullName)"
    }
}

# Check MessageController
Write-Host "`n`nChecking MessageController..." -ForegroundColor Cyan
$controllerPath = "Apex\Hermes\src\Api\Controllers\MessageController.php"
if (Test-Path $controllerPath) {
    $controllerContent = Get-Content $controllerPath | Select-String "use.*CMProvider" -SimpleMatch
    if ($controllerContent) {
        Write-Host "MessageController imports:" -ForegroundColor White
        $controllerContent | ForEach-Object { Write-Host "  $_" }
    }
}

# Check MessageRouter
Write-Host "`n`nChecking MessageRouter..." -ForegroundColor Cyan
$routerPath = "Apex\Hermes\src\Services\MessageRouter.php"
if (Test-Path $routerPath) {
    $routerContent = Get-Content $routerPath | Select-String "new CMProvider" -SimpleMatch
    if ($routerContent) {
        Write-Host "MessageRouter creates provider:" -ForegroundColor White
        $routerContent | ForEach-Object { Write-Host "  $_" }
    }
}

Write-Host "`n`nDone." -ForegroundColor Yellow