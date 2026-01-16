<?php
require 'db_connection.php';

// Permitir preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Obtener raw input JSON
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
// Log raw request for debugging
file_put_contents(__DIR__ . '/debug.log', "\n--- " . date('Y-m-d H:i:s') . " ---\nREQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\nRAW: " . $rawInput . "\nPOST: " . print_r($_POST, true) . "\n\n", FILE_APPEND);
// Fallback si el cliente envía form-data/urlencoded
if ($input === null && !empty($_POST)) {
    $input = $_POST;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $input;

    $nombre = trim($data['nombre'] ?? '');
    $apellido = trim($data['apellido'] ?? '');
    $fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
    $hora_entrada = $data['hora_entrada'] ?? null;
    $hora_salida = $data['hora_salida'] ?? null;
    $periodo_entrada = $data['periodo_entrada'] ?? null;
    $periodo_salida = $data['periodo_salida'] ?? null;
    $dias_laborables = $data['dias_laborables'] ?? '';

    // Validaciones básicas
    $required = ['nombre', 'fecha_nacimiento', 'hora_entrada', 'hora_salida', 'periodo_entrada', 'periodo_salida'];
    foreach ($required as $f) {
        if (empty($data[$f])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Campo requerido: $f"]);
            exit;
        }
    }

    // Normalizar valores para los ENUMs
    $periodo_entrada = in_array($periodo_entrada, ['AM', 'PM']) ? $periodo_entrada : 'AM';
    $periodo_salida = in_array($periodo_salida, ['AM', 'PM']) ? $periodo_salida : 'PM';

    $sql = "INSERT INTO directores (nombre, apellido, fecha_nacimiento, hora_entrada, hora_salida, periodo_entrada, periodo_salida, dias_laborables) 
            VALUES (:nombre, :apellido, :fecha_nacimiento, :hora_entrada, :hora_salida, :periodo_entrada, :periodo_salida, :dias_laborables)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':hora_entrada' => $hora_entrada,
            ':hora_salida' => $hora_salida,
            ':periodo_entrada' => $periodo_entrada,
            ':periodo_salida' => $periodo_salida,
            ':dias_laborables' => $dias_laborables
        ]);

        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Registro agregado exitosamente', 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        // Log error
        file_put_contents(__DIR__ . '/debug.log', "SQL ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar consulta en el servidor. Revisa debug.log en /database.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID es requerido para actualizar']);
        exit;
    }

    $id = (int)$input['id'];
    $nombre = trim($input['nombre'] ?? '');
    $apellido = trim($input['apellido'] ?? '');
    $fecha_nacimiento = $input['fecha_nacimiento'] ?? null;
    $hora_entrada = $input['hora_entrada'] ?? null;
    $hora_salida = $input['hora_salida'] ?? null;
    $periodo_entrada = $input['periodo_entrada'] ?? null;
    $periodo_salida = $input['periodo_salida'] ?? null;
    $dias_laborables = $input['dias_laborables'] ?? '';

    // Validaciones para update
    $required = ['nombre', 'fecha_nacimiento', 'hora_entrada', 'hora_salida', 'periodo_entrada', 'periodo_salida'];
    foreach ($required as $f) {
        if (empty($input[$f])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Campo requerido para actualizar: $f"]);
            exit;
        }
    }

    $periodo_entrada = in_array($periodo_entrada, ['AM', 'PM']) ? $periodo_entrada : 'AM';
    $periodo_salida = in_array($periodo_salida, ['AM', 'PM']) ? $periodo_salida : 'PM';

    $sql = "UPDATE directores SET nombre = :nombre, apellido = :apellido, fecha_nacimiento = :fecha_nacimiento, hora_entrada = :hora_entrada, hora_salida = :hora_salida, periodo_entrada = :periodo_entrada, periodo_salida = :periodo_salida, dias_laborables = :dias_laborables WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':hora_entrada' => $hora_entrada,
            ':hora_salida' => $hora_salida,
            ':periodo_entrada' => $periodo_entrada,
            ':periodo_salida' => $periodo_salida,
            ':dias_laborables' => $dias_laborables,
            ':id' => $id
        ]);

        echo json_encode(['success' => true, 'message' => 'Registro actualizado']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID es requerido para eliminar']);
        exit;
    }

    $id = (int)$input['id'];
    $sql = "DELETE FROM directores WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true, 'message' => 'Registro eliminado']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $sql = "SELECT * FROM directores WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) echo json_encode($result);
        else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
        }
        exit;
    }

    $sql = "SELECT * FROM directores";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>