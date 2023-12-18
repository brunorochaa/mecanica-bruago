<?php
session_start();
include '../model/cliente.class.php';
include '../dao/clientedao.class.php';
include '../util/padronizacao.class.php';
include '../util/validacao.class.php';

$erros = [];
if(!Validacao::validarNome($_POST['nome'])) {
    $erros[] = "Nome inválido!";
}

if(!Validacao::validarSexo($_POST['sexo'])) {
    $erros[] = "Sexo inválido!";
}

if(!Validacao::validarCep($_POST['cep'])) {
    $erros[] = "CEP inválido!";
}

if(!Validacao::validarNumeroCasa($_POST['numeroCasa'])) {
    $erros[] = "Número da casa inválido!";
}

if(!Validacao::validarTelefone($_POST['telefone'])) {
    $erros[] = "Telefone inválido!";
}

if(count($erros) == 0) {
    $cliente = new Cliente();

    // $cliente->idCliente = ;
    $cliente->nome = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['nome'])));
    $cliente->sexo = Padronizacao::antiXSS($_POST['sexo']);
    $cliente->dataNascimento = Padronizacao::antiXSS($_POST['dataNascimento']);
    $cliente->cep = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['cep']));
    $cliente->numeroCasa = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['numeroCasa']));
    $cliente->telefone = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['telefone']));

    $clienteDAO = new clienteDAO;
    $clienteDAO->cadastrar($cliente);
    header("location:../buscar.php");
} else {
    $_SESSION['post'] = serialize($_POST);
    $_SESSION['erros'] = serialize($erros);
    header("location:../cadastro.php");
}