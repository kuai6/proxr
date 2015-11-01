@ECHO off

set phpCli=php
set projectDir="%~dp0\.."
set moduleDirs="DIR /a:d /b ..\module"

for /f %%i in ('dir /a:d /b %projectDir%\vendor\mte') do (
  cd "%projectDir%\vendor\mte\%%i"
  php "%projectDir%\vendor\zendframework\zendframework\bin\classmap_generator.php" %*
)
cd "%projectDir%\bin\"
