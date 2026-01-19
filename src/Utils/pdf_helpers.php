<?php

function pdf_txt($s): string
{
    $s = (string)$s;
    $out = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s);
    return $out !== false ? $out : $s;
}

function money_eur(float $n): string
{
    return number_format($n, 2, ',', ' ') . ' EUR';
}
