<?php
class Padronizacao
{
    public static function padronizarMaiusculasMinusculas($valor): string
    {
        return ucwords(mb_strtolower($valor));
    }

    public static function antiXSS($valor): string
    {
        return htmlentities($valor);
    }

    public static function antiEspaco($valor): string
    {
        return trim($valor);
    }
}