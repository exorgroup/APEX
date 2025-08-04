# PowerShell script to fix database connections in Hermes files
# Save as fix-hermes-db-connection.ps1

Write-Host "Fixing Database Connections in Hermes Files..." -ForegroundColor Yellow
Write-Host "=============================================" -ForegroundColor Yellow

# Files to fix
$filesToFix = @(
    "Apex\Hermes\src\Providers\CMTelecom\CMProvider.php",
    "Apex\Hermes\src\Providers\CMTelecom\CMProviderSimple.php",
    "Apex\Hermes\src\Api\Middleware\ApiAuthentication.php",
    "Apex\Hermes\src\Console\Commands\GenerateApiKeyCommand.php"
)

foreach ($file in $filesToFix) {
    if (Test-Path $file) {
        Write-Host "`nProcessing: $file" -ForegroundColor Cyan
        
        # Read the file
        $content = Get-Content $file -Raw
        $originalContent = $content
        
        # Replace DB::table with DB::connection('hermes')->table
        $content = $content -replace "DB::table\('hermes_api_keys'\)", "DB::connection('hermes')->table('hermes_api_keys')"
        
        # Check if changes were made
        if ($content -ne $originalContent) {
            # Backup original
            $backupPath = $file + ".backup"
            Set-Content -Path $backupPath -Value $originalContent
            Write-Host "  Created backup: $backupPath" -ForegroundColor Gray
            
            # Write updated content
            Set-Content -Path $file -Value $content
            Write-Host "  ✓ Fixed database connection" -ForegroundColor Green
        } else {
            # Check if it already has the correct connection
            if ($content -match "DB::connection\('hermes'\)") {
                Write-Host "  ✓ Already using correct connection" -ForegroundColor Green
            } else {
                Write-Host "  ⚠ No DB::table('hermes_api_keys') found" -ForegroundColor Yellow
            }
        }
    } else {
        Write-Host "`n✗ File not found: $file" -ForegroundColor Red
    }
}

Write-Host "`nClearing Laravel caches..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear
php artisan route:clear

Write-Host "`nDone! Try testing the API again." -ForegroundColor Green