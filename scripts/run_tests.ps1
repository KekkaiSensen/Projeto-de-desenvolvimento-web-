# start_run_tests.ps1

$Root = Split-Path $PSScriptRoot -Parent
$Root = Split-Path $PSScriptRoot -Parent
Write-Host "Executando testes Unitários (PHPUnit)..."

$phpunitPath = "$Root\vendor\bin\phpunit.bat"
if (-not (Test-Path $phpunitPath)) {
    $phpunitPath = "$Root\vendor\bin\phpunit"
}

& $phpunitPath
if ($LASTEXITCODE -ne 0) { 
    Write-Error "Testes Unitários falharam! Abortando..."
    exit 1 
}

Write-Host "Iniciando servidor PHP em localhost:8000..."
$phpProcess = Start-Process -FilePath "php" -ArgumentList "-S localhost:8000 -t `"$Root`"" -PassThru -NoNewWindow

Write-Host "Aguardando servidor iniciar..."
Start-Sleep -Seconds 3

Write-Host "Executando testes Cypress..."
Push-Location "$Root\Testes"
try {
    npm run test
}
finally {
    Pop-Location
    Write-Host "Encerrando servidor PHP..."
    Stop-Process -Id $phpProcess.Id -Force
}
