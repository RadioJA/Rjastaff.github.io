# Script de prueba para endpoints de la app RJA
# Ejecutar desde PowerShell en Windows con Apache+MySQL corriendo

$base = "http://localhost/rja/database"

function Test-Endpoint($name, $url, $method, $body) {
    Write-Host "\n== Testing $name ($method) =="
    try {
        if ($body) {
            $json = $body | ConvertTo-Json -Depth 4
            $resp = Invoke-RestMethod -Uri $url -Method $method -Body $json -ContentType 'application/json'
        } else {
            $resp = Invoke-RestMethod -Uri $url -Method $method
        }
        Write-Host "Response:`n" ($resp | ConvertTo-Json -Depth 6)
    } catch {
        Write-Host "ERROR: $_" -ForegroundColor Red
    }
}

# Test Directores
$body = @{
    nombre = 'PS Prueba'
    apellido = 'Direct'
    fecha_nacimiento = '1985-07-07'
    hora_entrada = '08:00'
    hora_salida = '12:00'
    periodo_entrada = 'AM'
    periodo_salida = 'PM'
    dias_laborables = 'Lunes,Martes'
}
Test-Endpoint -name 'Directores POST' -url "$base/directores.php" -method 'POST' -body $body
Test-Endpoint -name 'Directores GET' -url "$base/directores.php" -method 'GET' -body $null

# Test Locutores
$body = @{
    nombre = 'PS Loc'
    apellido = 'Loc'
    fecha_nacimiento = '1990-01-01'
    hora_inicio = '10:00'
    hora_fin = '12:00'
    periodo_inicio = 'AM'
    periodo_fin = 'PM'
    dias_trabajo = 'Miercoles'
}
Test-Endpoint -name 'Locutores POST' -url "$base/locutores.php" -method 'POST' -body $body
Test-Endpoint -name 'Locutores GET' -url "$base/locutores.php" -method 'GET' -body $null

# Test Moderadores
$body = @{
    nombre = 'PS Mod'
    apellido = 'Mod'
    fecha_nacimiento = '1992-02-02'
    hora_inicio = '14:00'
    hora_fin = '16:00'
    periodo_inicio = 'PM'
    periodo_fin = 'PM'
    dias_moderacion = 'Jueves'
}
Test-Endpoint -name 'Moderadores POST' -url "$base/moderadores.php" -method 'POST' -body $body
Test-Endpoint -name 'Moderadores GET' -url "$base/moderadores.php" -method 'GET' -body $null

Write-Host "\nTests completos. Si ves errores, copia la salida y pégala para depuración."