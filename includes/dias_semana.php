<?php

/*
    DÍAS DE LA SEMANA
    El valor (clave) es el que se guarda en la base de datos
    y el texto es el que se muestra en pantalla.
*/

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
