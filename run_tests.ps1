# start_run_tests.ps1

Write-Host "Iniciando servidor PHP em localhost:8000..."
$phpProcess = Start-Process -FilePath "php" -ArgumentList "-S localhost:8000 -t ." -PassThru -NoNewWindow

Write-Host "Aguardando servidor iniciar..."
Start-Sleep -Seconds 3

Write-Host "Executando testes Cypress..."
Push-Location Testes
try {
    npm run test
}
finally {
    Pop-Location
    Write-Host "Encerrando servidor PHP..."
    Stop-Process -Id $phpProcess.Id -Force
}
