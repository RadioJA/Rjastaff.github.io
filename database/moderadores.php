<?php
require 'db_connection.php';

// Permitir preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Debug/logging
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
file_put_contents(__DIR__ . '/debug.log', "\n--- " . date('Y-m-d H:i:s') . " ---\nREQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\nRAW: " . $rawInput . "\nPOST: " . print_r($_POST, true) . "\n\n", FILE_APPEND);
if ($input === null && !empty($_POST)) {
    $input = $_POST;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $input;

    $nombre = trim($data['nombre'] ?? '');
    $apellido = trim($data['apellido'] ?? '');
    $fecha_nacimiento = $data['fecha_nacimiento'] ?? null;
    $hora_inicio = $data['hora_inicio'] ?? null;
    $hora_fin = $data['hora_fin'] ?? null;
    $periodo_inicio = $data['periodo_inicio'] ?? null;
    $periodo_fin = $data['periodo_fin'] ?? null;
    $dias_moderacion = $data['dias_moderacion'] ?? '';

    $required = ['nombre', 'fecha_nacimiento', 'hora_inicio', 'hora_fin', 'periodo_inicio', 'periodo_fin'];
    foreach ($required as $f) {
        if (empty($data[$f])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Campo requerido: $f"]);
            exit;
        }
    }

    $periodo_inicio = in_array($periodo_inicio, ['AM', 'PM']) ? $periodo_inicio : 'AM';
    $periodo_fin = in_array($periodo_fin, ['AM', 'PM']) ? $periodo_fin : 'PM';

    $sql = "INSERT INTO moderadores (nombre, apellido, fecha_nacimiento, hora_inicio, hora_fin, periodo_inicio, periodo_fin, dias_moderacion) 
            VALUES (:nombre, :apellido, :fecha_nacimiento, :hora_inicio, :hora_fin, :periodo_inicio, :periodo_fin, :dias_moderacion)";

    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':periodo_inicio' => $periodo_inicio,
            ':periodo_fin' => $periodo_fin,
            ':dias_moderacion' => $dias_moderacion
        ]);

        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Registro agregado exitosamente', 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
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
    $hora_inicio = $input['hora_inicio'] ?? null;
    $hora_fin = $input['hora_fin'] ?? null;
    $periodo_inicio = $input['periodo_inicio'] ?? null;
    $periodo_fin = $input['periodo_fin'] ?? null;
    $dias_moderacion = $input['dias_moderacion'] ?? '';

    $required = ['nombre', 'fecha_nacimiento', 'hora_inicio', 'hora_fin', 'periodo_inicio', 'periodo_fin'];
    foreach ($required as $f) {
        if (empty($input[$f])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Campo requerido para actualizar: $f"]);
            exit;
        }
    }

    $periodo_inicio = in_array($periodo_inicio, ['AM', 'PM']) ? $periodo_inicio : 'AM';
    $periodo_fin = in_array($periodo_fin, ['AM', 'PM']) ? $periodo_fin : 'PM';

    $sql = "UPDATE moderadores SET nombre = :nombre, apellido = :apellido, fecha_nacimiento = :fecha_nacimiento, hora_inicio = :hora_inicio, hora_fin = :hora_fin, periodo_inicio = :periodo_inicio, periodo_fin = :periodo_fin, dias_moderacion = :dias_moderacion WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':periodo_inicio' => $periodo_inicio,
            ':periodo_fin' => $periodo_fin,
            ':dias_moderacion' => $dias_moderacion,
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
    $sql = "DELETE FROM moderadores WHERE id = :id";
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
        $sql = "SELECT * FROM moderadores WHERE id = :id";
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

    $sql = "SELECT * FROM moderadores";
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);
?>