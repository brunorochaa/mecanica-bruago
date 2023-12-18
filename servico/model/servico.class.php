<?php
class Servico
{
    private $idServico; //auto_increment
    private $descricaoServico; //string, varchar(200)
    private $filial; //select, varchar(50)
    private $nomeMecanico; //string, varchar(100)
    private $carro; //string, varchar (150)
    private $cor; // string, varchar (100)
    private $placa; //string, varchar (8)
    private $tipoCombustivel; //select, varchar (30)
    private $urgencia; //radio, varchar(30)

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function __toString()
    {
        return nl2br("
                      Código: $this->idServico
                      Serviço: $this->descricaoServico
                      Filial: $this->filial
                      Mecânico: $this->nomeMecanico
                      Carro: $this->carro
                      Cor: $this->cor
                      Placa: $this->placa
                      Tipo de Combústivel: $this->tipoCombustivel
                      Urgência: $this->urgencia
        ");
    }
}