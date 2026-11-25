<?php

// Días de la semana: clave (se guarda en la BD) => texto (se muestra en pantalla)

function diasSemana()
{
    return array(
        "lunes" => "Lunes",
        "martes" => "Martes",
        "miercoles" => "Miércoles",
        "jueves" => "Jueves",
        "viernes" => "Viernes",
        "sabado" => "Sábado",
        "domingo" => "Domingo"
    );
}

function nombreDia($valor)
{
    $dias = diasSemana();

    return $dias[$valor] ?? "Sin asignar";
}
