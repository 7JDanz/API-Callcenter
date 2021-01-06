@echo off
set ip_address_string="IPv4"
rem Uncomment the following line when using older versions of Windows without IPv6 support (by removing "rem")
rem set ip_address_string="IP Address"
echo SIRVIENDO EN:
for /f "usebackq tokens=2 delims=:" %%f in (`ipconfig ^| findstr /c:%ip_address_string%`) do (
    echo %%f:8586
    echo Probar: %%f:8586/api/menu/menu-buscar/IDMenu/38D97934-A4B4-E911-80E2-000D3A019254?descripcion=box
)
php -S 0.0.0.0:8586 -t public
