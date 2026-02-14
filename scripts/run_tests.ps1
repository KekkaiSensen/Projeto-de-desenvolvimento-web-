# start_run_tests.ps1

$Root = Split-Path $PSScriptRoot -Parent
Write-Host "Raiz do projeto: $Root"

Write-Host "Executando testes Unitários (PHPUnit)..."
$phpunitPath = "$Root\vendor\bin\phpunit.bat"
if (-not (Test-Path $phpunitPath)) {
    $phpunitPath = "$Root\vendor\bin\phpunit"
}

Write-Host "Usando PHPUnit em: $phpunitPath"

# Executa PHPUnit apontando para a configuração correta
& $phpunitPath -c "$Root\phpunit.xml" --testdox
if ($LASTEXITCODE -ne 0) { 
    Write-Error "Testes Unitários falharam! Abortando..."
    exit 1 
}

Write-Host "Iniciando servidor PHP em localhost:8000..."
$phpProcess = Start-Process -FilePath "php" -ArgumentList "-S localhost:8000 -t `"$Root`"" -PassThru -NoNewWindow

Write-Host "Aguardando servidor iniciar..."
Start-Sleep -Seconds 3

Write-Host "Executando testes Cypress..."
# Garante que estamos na raiz para o npx funcionar bem com o config
Push-Location "$Root\tests\E2E"
try {
    # start-server-and-test ou execução direta
    # Como já iniciamos o servidor manualmente acima, rodamos direto o cypress com os testes e2e
    npx cypress run --spec "cypress/e2e/sistema_notificacao.cy.js,cypress/e2e/sistema_cupom.cy.js"
}
finally {
    Pop-Location
    Write-Host "Encerrando servidor PHP..."
    Stop-Process -Id $phpProcess.Id -Force
}
