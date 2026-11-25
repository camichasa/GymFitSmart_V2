<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'alumno') {
    header('Location: ../auth/iniciar_sesion.html');
    exit();
}

// Importación de librería FPDF
require '../libs/fpdf/fpdf.php';
require '../config/conexion_bd.php';
require '../includes/funciones_rutina.php';

$rutina = obtenerRutina($pdo, (int)$_SESSION['id_usuario']);

$nombresDia = [
    'lunes' => 'Lunes',
    'martes' => 'Martes',
    'miercoles' => 'Miércoles',
    'jueves' => 'Jueves',
    'viernes' => 'Viernes',
    'sabado' => 'Sábado',
    'domingo' => 'Domingo'
];

// Conversión de acentos para FPDF (ISO-8859-1)
function t(string $texto): string
{
    return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
}

$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 12, t('GymFit Smart - Mi rutina semanal'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(28, 10, t('Día'), 1, 0, 'C', true);
$pdf->Cell(70, 10, 'Ejercicio', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Series x Reps', 1, 0, 'C', true);
$pdf->Cell(47, 10, 'Carga', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
foreach ($rutina as $dia => $ejercicios) {
    if (empty($ejercicios)) {
        $pdf->Cell(28, 10, t($nombresDia[$dia]), 1);
        $pdf->Cell(162, 10, t('Día de descanso'), 1, 1);
        continue;
    }
    foreach ($ejercicios as $i => $e) {
        $carga = $e['peso'] !== null ? $e['peso'] . ' kg' : 'Peso corporal';
        $pdf->Cell(28, 10, $i === 0 ? t($nombresDia[$dia]) : '', 1);
        $pdf->Cell(70, 10, t($e['nombre']), 1);
        $pdf->Cell(45, 10, $e['series'] . ' x ' . $e['repeticiones'], 1);
        $pdf->Cell(47, 10, t($carga), 1, 1);
    }
}

// Descarga del PDF ('D' descarga, 'I' abre en el navegador)
$pdf->Output('D', 'rutina_semanal.pdf');
