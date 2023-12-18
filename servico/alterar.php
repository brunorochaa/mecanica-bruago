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
        <title>Alterar Dados do Serviço</title>
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

                    <li class="nav-item"><a class="nav-link" href="cadastro.php">CADASTRAR SERVIÇO<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="buscar.php">BUSCAR DADOS<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../cliente/cadastro.php">CADASTRAR CLIENTE<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../produto/index.php">NOSSOS PRODUTOS<span class="sr-only"></span></a></li>
                </ul>
            </div>
        </nav>
        <h1 class="jumbotron titulo">Alterar Dados de Serviço</h1>
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
            include "model/servico.class.php";
            include "dao/servicodao.class.php";

            $servicoDAO = new ServicoDAO;
            $servicos = $servicoDAO->filtrarServico("codigo", $_GET['id']);

            $servico = $servicos[0];
        }
        ?>
        <form name="alterar" method="post" action="alterar.php">
            <div>
                <input type="text" name="codigo" value="<?php echo $servico->idServico ?? ""; ?>" readonly hidden>
            </div>
            <h6>Digite a descrição do serviço:</h6>
            <div class="form-group">
                <input type="text" name="descricaoServico" placeholder="descrição do serviço" pattern="[0-9a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,200}" required autofocus value="<?php echo $servico->descricaoServico ?? ""; ?>">
            </div>
            <h6>Selecione a filial da oficina:</h6>
            <div class="form-group">
                <select name="filial">
                    <option value="assisBrasil" <?php if(isset($servico))  if($servico->filial=='assisBrasil') echo "selected"; ?>>Filial Assis Brasil</option>
                    <option value="santaRosa" <?php if(isset($servico))  if($servico->tipoCombustivel=='santaRosa') echo "selected"; ?>>Filial Santa Rosa</option>
                </select>
            </div>
            <h6>Digite o nome do mecânico que cuidará do serviço:</h6>
            <div class="form-group">
                <input type="text" name="nomeMecanico" placeholder="nome do mecânico" pattern="[0-9a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}" required value="<?php echo $servico->nomeMecanico ?? ""; ?>">
            </div>
            <h6>Digite o carro:</h6>
            <div class="form-group">
                <input type="text" name="carro" placeholder="marca, modelo" pattern="[0-9a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ,]{2,100}" required value="<?php echo $servico->carro ?? ""; ?>">
            </div>
            <h6>Digite a cor do carro:</h6>
            <div class="form-group">
                <input type="text" name="cor" placeholder="cor do carro" pattern="[0-9a-zA-ZãÃáÁàÀêÊéÉèÈíÍìÌôÔõÕóÓòÒúÚùÙûÛçÇ ]{2,100}" required value="<?php echo $servico->cor ?? ""; ?>">
            </div>
            <h6>Digite a placa do carro:</h6>
            <div class="form-group">
                <input type="text" name="placa" placeholder="AAA-0000" pattern="[A-Z]{3}-[0-9]{4}" required value="<?php echo $servico->placa ?? ""; ?>">
            </div>
            <h6>Selecione o tipo de combustível do carro:</h6>
            <div class="form-group">
                <select name="tipoCombustivel">
                    <option value="comum" <?php if(isset($servico))  if($servico->tipoCombustivel=='comum') echo "selected"; ?>>Comum</option>
                    <option value="diesel" <?php if(isset($servico))  if($servico->tipoCombustivel=='diesel') echo "selected"; ?>>Diesel</option>
                    <option value="etanol" <?php if(isset($servico))  if($servico->tipoCombustivel=='etanol') echo "selected"; ?>>Etanol</option>
                    <option value="gas" <?php if(isset($servico))  if($servico->tipoCombustivel=='gas') echo "selected"; ?>>Gás</option>
                    <option value="hibrido" <?php if(isset($servico))  if($servico->tipoCombustivel=='hibrido') echo "selected"; ?>>Híbrido</option>
                </select>
            </div>
            <h6>Selecione a urgência do serviço:</h6>
            <div class="form-group">
                <label><input type="radio" name="urgencia" value="semUrgencia" <?php if(isset($servico))  if($servico->urgencia=='semUrgencia') echo "checked"; ?> pattern="(semUrgencia|comUrgencia)">Sem urgência</label>
                <label><input type="radio" name="urgencia" value="comUrgencia" <?php if(isset($servico))  if($servico->urgencia=='comUrgencia') echo "checked"; ?> pattern="(semUrgencia|comUrgencia)">Com Urgência</label>
            </div>
            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="alterar"value="Alterar">
            </div>
        </form>
        <?php
        if(isset($_POST['alterar'])) {
            include_once 'model/servico.class.php';
            include_once 'dao/servicodao.class.php';
            include 'util/padronizacao.class.php';

            $servicoAlterar = new Servico();

            $servicoAlterar->idServico = $_POST['codigo'];
            $servicoAlterar->descricaoServico = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['descricaoServico']));
            $servicoAlterar->filial = Padronizacao::antiXSS($_POST['filial']);
            $servicoAlterar->nomeMecanico = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['nomeMecanico'])));
            $servicoAlterar->carro = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['carro'])));
            $servicoAlterar->cor = Padronizacao::antiEspaco(Padronizacao::antiXSS(Padronizacao::padronizarMaiusculasMinusculas($_POST['cor'])));
            $servicoAlterar->placa = Padronizacao::antiEspaco(Padronizacao::antiXSS($_POST['placa']));
            $servicoAlterar->tipoCombustivel = Padronizacao::antiXSS($_POST['tipoCombustivel']);
            $servicoAlterar->urgencia = Padronizacao::antiXSS($_POST['urgencia']);

            $servicoDAO = new servicoDAO;
            $servicoDAO->alterarServico($servicoAlterar);
            header("location:buscar.php");
        }
        ?>
    </body>
</html>