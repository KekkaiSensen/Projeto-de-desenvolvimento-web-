@echo off
echo ==========================================
echo       Instalando Dependencias
echo ==========================================

echo.
echo [1/3] Verificando Redis...
set "REDIS_URL=https://github.com/tporadowski/redis/releases/download/v5.0.14.1/Redis-x64-5.0.14.1.msi"
set "REDIS_INSTALLER=Redis-x64-5.0.14.1.msi"

rem Check default install location
if exist "C:\Program Files\Redis\redis-server.exe" (
    echo Redis ja encontrado em C:\Program Files\Redis.
) else (
    echo Redis nao encontrado. Iniciando download...
    powershell -Command "Invoke-WebRequest -Uri '%REDIS_URL%' -OutFile '%REDIS_INSTALLER%'"
    
    echo Instalando Redis (Solicitacao de permissao pode aparecer)...
    rem /quiet installs silently, but we might want /passive to show progress bar without interaction
    msiexec /i %REDIS_INSTALLER% /passive /norestart ADDLOCAL=ALL
    
    echo Limpando instalador...
    if exist %REDIS_INSTALLER% del %REDIS_INSTALLER%
    echo Instalacao do Redis concluida.
)

echo.
echo [2/3] Instalando dependencias do Backend (PHP)...
where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo Erro: Composer nao encontrado no PATH. Por favor instale o Composer.
) else (
    call cd ..
    call composer install
    call cd scripts
)

echo.
echo [3/3] Instalando dependencias de Testes (Node.js)...
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo Erro: NPM nao encontrado no PATH. Por favor instale o Node.js.
) else (
    if exist "..\Testes" (
        cd ..\Testes
        call npm install
        cd ..\scripts
    ) else (
        echo Pasta 'Testes' nao encontrada. Pulei o npm install.
    )
)

echo.
echo ==========================================
echo       Instalacao Concluida!
echo ==========================================
echo Verifique se nao ocorreram erros acima.
pause
