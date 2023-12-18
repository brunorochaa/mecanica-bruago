<?php
class Validacao
{
    public static function validarNome($valor):bool
    {
        $expressao = "/^[a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}$/";
        return preg_match($expressao, $valor);
    }

    public static function validarSexo($valor):bool
    {
        $expressao = "/^(masculino|feminino)$/";
        return preg_match($expressao, $valor);
    }

    /*public static function validarDataNascimento($valor):bool
    {
        $expressao = "/^[\d]{2}/[\d]{2}/[\d]{4}/";
        return preg_match($expressao, $valor);
    }*/

    public static function validarCep($valor):bool
    {
        $expressao = "/^[0-9]{2}.[0-9]{3}-[0-9]{3}$/";
        return preg_match($expressao, $valor);
    }

    public static function validarNumeroCasa($valor):bool
    {
        $expressao = "/^[A-Z0-9]{2,10}$/";
        return preg_match($expressao, $valor);
    }
    
    public static function validarTelefone($valor):bool
    {
        $expressao = "/^[0-9]{2,3}[0-9]{8,9}$/";
        return preg_match($expressao, $valor);
    }
}