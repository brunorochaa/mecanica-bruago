<?php
class Validacao
{
    public static function validarDescricaoServico($valor):bool
    {
        $expressao = "/^[0-9a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,200}$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarFilial($valor):bool
    {
        $expressao = "/^(assisBrasil|santaRosa)$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarNomeMecanico($valor):bool
    {
        $expressao = "/^[a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarCarro($valor):bool
    {
        $expressao = "/^[a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ, ]{2,100}$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarCor($valor):bool
    {
        $expressao = "/^[a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}$/";
        return preg_match($expressao, $valor);
    }
  
  
    public static function validarPlaca($valor):bool
    {
        $expressao = "/^[A-Z]{3}-[0-9]{4}$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarTipoCombustivel($valor):bool
    {
        $expressao = "/^(comum|diesel|etanol|gas|hibrido)$/";
        return preg_match($expressao, $valor);
    }
  
    public static function validarUrgencia($valor):bool
    {
        $expressao = "/^(semUrgencia|comUrgencia)$/";
        return preg_match($expressao, $valor);
    }
}