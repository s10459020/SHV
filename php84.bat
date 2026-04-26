@echo off
setlocal EnableExtensions EnableDelayedExpansion

set "ROOT=%~dp0"
set "PHP_EXE=%ROOT%.tool\php-8.4.20-nts-Win32-vs17-x64\php.exe"
set "HOST=0.0.0.0"
set "PORT=%~1"

if "%PORT%"=="" set "PORT=8000"

if not exist "%PHP_EXE%" (
  echo [ERROR] php.exe not found:
  echo %PHP_EXE%
  exit /b 1
)

echo.
echo PHP 8.4 server is starting...
echo Root   : %ROOT%
echo PHP    : %PHP_EXE%
echo Bind   : http://%HOST%:%PORT%
echo Local  : http://127.0.0.1:%PORT%
echo.
echo Press Ctrl+C to stop.
echo.

pushd "%ROOT%"
"%PHP_EXE%" -S %HOST%:%PORT% -t .
popd

endlocal