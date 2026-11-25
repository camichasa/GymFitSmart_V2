<?php
function obtenerRutina(PDO $pdo, int $idAlumno): array
{
    $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
    $rutina = array_fill_keys($dias, []);

    $stmt = $pdo->prepare("
        SELECT r.dia, r.nombre AS rutina, e.nombre, e.series,
               e.repeticiones, e.peso_sugerido, e.notas
        FROM rutinas r
        INNER JOIN ejercicios e ON e.rutina_id = r.id
        WHERE r.alumno_id = ?
        AND r.dia IS NOT NULL
        ORDER BY r.id, e.orden, e.id
    ");
    $stmt->execute([$idAlumno]);

    foreach ($stmt as $f) {
        $rutina[$f['dia']][] = [
            'rutina'       => $f['rutina'],
            'nombre'       => $f['nombre'],
            'series'       => (int)$f['series'],
            'repeticiones' => (int)$f['repeticiones'],
            'peso'         => $f['peso_sugerido'] !== null ? (float)$f['peso_sugerido'] : null,
            'notas'        => $f['notas'],
        ];
    }
    return $rutina;
}
