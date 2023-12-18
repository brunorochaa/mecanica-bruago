<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Alterar Dados do Cliente</title>
        <link href="../css/style.css" rel="stylesheet">
        <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <script src="../node_modules/jquery/dist/jquery.min.js"></script>
        <script src="../node_modules/ajax/lib/ajax.js"></script>
        <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="../index.php">Mecânica</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../index.php">HOME</a></li>

                    <li class="nav-item"><a class="nav-link" href="cadastro.php">CADASTRAR CLIENTE<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="buscar.php">BUSCAR DADOS<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../servico/cadastro.php">CADASTRAR SERVIÇO<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../produto/index.php">NOSSOS PRODUTOS<span class="sr-only"></span></a></li>
                </ul>
            </div>
        </nav>
        <h1 class="jumbotron titulo">Alterar Dados de Cliente</h1>
        <?php
        if(isset($_SESSION['erros'])) {
            $erros = unserialize($_SESSION['erros']);
            foreach($erros as $erro) {
                echo "<p>".$erro."</p>";
            }
            $post = unserialize($_SESSION['post']);
            unset($_SESSION['erros']);
        }
        ?>
        <?php
        if(isset($_GET['id'])) {
            include "model/cliente.class.php";
            include "dao/clientedao.class.php";

            $clienteDAO = new ClienteDAO;
            $clientes = $clienteDAO->filtrarCliente("codigo", $_GET['id']);

            $cliente = $clientes[0];
        }
        ?>
        <form name="alterar" method="post" action="alterar.php">
            <div>
                <input type="text" name="codigo" value="<?php echo $cliente->idCliente ?? ""; ?>" readonly hidden>
            </div>
            <h6>Digite seu nome:</h6>
            <div>
                <input type="text" name="nome" placeholder="nome" pattern="[a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}" required  autofocus value="<?php echo $cliente->nome ?? ""; ?>">
            </div>
            <h6>Marque seu sexo:</h6>
            <div class="form-group">
                <label><input type="radio" name="sexo" value="masculino" <?php if(isset($cliente))  if($cliente->sexo=='masculino') echo "checked"; ?> pattern="(masculino|feminino)">Masculino</label>
                <label><input type="radio" name="sexo" value="feminino" <?php if(isset($cliente))  if($cliente->sexo=='feminino') echo "checked"; ?> pattern="(masculino|feminino)">Feminino</label>
            </div>
            <h6>Digite sua data de nascimento:</h6>
            
            <div class="form-group">
                <input type="date" name="dataNascimento" required value="<?php echo $cliente->dataNascimento ?? ""; ?>">
            </div>
            <h6>Digite seu CEP:</h6>
            <div class="form-group">
                <input type="text" name="cep" placeholder="00.000-000" pattern="[0-9]{2}.[0-9]{3}-[0-9]{3}" required value="<?php echo $cliente->cep ?? ""; ?>">
            </div>
            <h6>Digite o número da sua casa:</h6>
            <div class="form-group">
                <input type="text" name="numeroCasa" placeholder="número da casa" pattern="[A-Z0-9]{2,10}" required value="<?php echo $cliente->numeroCasa ?? ""; ?>">
            </div>
            <h6>Digite seu telefone:</h6>
            <div class="form-group">
                <input type="text" name="telefone" placeholder="telefone" pattern="[0-9]{2,3}[0-9]{8,9}" required value="<?php echo $cliente->telefone ?? ""; ?>">
            </div>
            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="alterar" value="Alterar">
            </div>
        </form>
        <?php
        if(isset($_POST['alterar'])) {
            include_once 'model/cliente.class.php';
            include_once 'dao/clientedao.class.php';
            include 'util/padronizacao.class.php';

            $clienteAlterar = new Cliente();

            $clienteAlterar->idCliente = $_POST['codigo'];
            $clienteAlterar->nome = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['nome'])));
            $clienteAlterar->sexo = Padronizacao::antiXSS($_POST['sexo']);
            $clienteAlterar->dataNascimento = Padronizacao::antiXSS($_POST['dataNascimento']);
            $clienteAlterar->cep = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['cep']));
            $clienteAlterar->numeroCasa = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['numeroCasa']));
            $clienteAlterar->telefone = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['telefone']));

            $clienteDAO = new clienteDAO;
            $clienteDAO->alterarCliente($clienteAlterar);
            header("location:buscar.php");
        }
        ?>
    </body>
</html>