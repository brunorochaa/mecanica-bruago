<?php
session_start();
include '../model/servico.class.php';
include '../dao/servicodao.class.php';
include '../util/padronizacao.class.php';
include '../util/validacao.class.php';

$erros = [];
if(!Validacao::validarDescricaoServico($_POST['descricaoServico'])) {
    $erros[] = "Descrição de serviço inválido!";
}

if(!Validacao::validarFilial($_POST['filial'])) {
    $erros[] = "Filial inválida!";
}

if(!Validacao::validarNomeMecanico($_POST['nomeMecanico'])) {
    $erros[] = "Nome do mecânico inválido!";
}

if(!Validacao::validarCarro($_POST['carro'])) {
    $erros[] = "Carro inválido!";
}

if(!Validacao::validarCor($_POST['cor'])) {
    $erros[] = "Cor do carro inválido!";
}

if(!Validacao::validarPlaca($_POST['placa'])) {
    $erros[] = "Placa do carro inválida!";
}

if(!Validacao::validarTipoCombustivel($_POST['tipoCombustivel'])) {
    $erros[] = "Tipo de combustível do carro inválido!";
}

if(!Validacao::validarUrgencia($_POST['urgencia'])) {
    $erros[] = "Nível de urgência inválida!";
}

if(count($erros) == 0) {
    $servico = new Servico();

    //$servico->idServico = ;
    $servico->descricaoServico = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['descricaoServico']));
    $servico->filial = Padronizacao::antiXSS($_POST['filial']);
    $servico->nomeMecanico = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['nomeMecanico'])));
    $servico->carro = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['carro'])));
    $servico->cor = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['cor'])));
    $servico->placa = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['placa']));
    $servico->tipoCombustivel = Padronizacao::antiXSS($_POST['tipoCombustivel']);
    $servico->urgencia = Padronizacao::antiXSS($_POST['urgencia']);

    $servicoDAO = new servicoDAO;
    $servicoDAO->cadastrar($servico);
    header("location:../buscar.php");
} else {
    $_SESSION['post'] = serialize($_POST);
    $_SESSION['erros'] = serialize($erros);
    header("location:../cadastro.php");
}