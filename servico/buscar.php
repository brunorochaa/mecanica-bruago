<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Buscar Dados do Serviço</title>
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

                    <li class="nav-item active"><a class="nav-link" href="buscar.php">BUSCAR DADOS<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../cliente/cadastro.php">CADASTRAR CLIENTE<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../produto/index.php">NOSSOS PRODUTOS<span class="sr-only"></span></a></li>
                </ul>
            </div>
        </nav>
        <h1 class="jumbotron titulo">Buscar Dados de Serviços</h1>
        <?php
        include 'dao/servicodao.class.php';
        include 'model/servico.class.php';

        $servicoDAO = new servicoDAO;
        $servicos = $servicoDAO->buscarServico();

        if(count($servicos) == 0) {
            echo "<h1>Não há servicos no banco!</h1>";
            echo "</body>";
            echo "</html>";
            die();
        }
        ?>
        <form name="pesquisa" method="post" action="">
          <div class="row">
            <div class="form-group col-md-6">
              <input type="text" name="pesquisa"
              class="form-control" placeholder="Digite sua pesquisa">
            </div>
            <div class="form-group col-md-6">
              <select name="filtro" class="form-control">
                <option value="todos">Todos</option>
                <option value="codigo">Código</option>
                <option value="descricaoServico">Descrição do Serviço</option>
                <option value="filial">Filial</option>
                <option value="nomeMecanico">Nome do Mecânico</option>
                <option value="carro">Carro</option>
                <option value="cor">Cor</option>
                <option value="placa">Placa</option>
                <option value="tipoCombustivel">Tipo de Combustível</option>
                <option value="urgencia">Urgência</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary btn-block">
          </div>
        </form>
        <?php
        if(isset($_POST['filtrar'])){
          include_once 'dao/servicodao.class.php';
          $pesquisa = $_POST['pesquisa'];
          $filtro = $_POST['filtro'];
          $servicoDAO = new ServicoDAO;
          $servicos = $servicoDAO->filtrarServico($filtro, $pesquisa);

          if(count($servicos) == 0){
            echo "<h2>Sua pesquisa não retornou nenhum serviço!</h2>";
            echo "</body>";
            echo "</html>";
            die();
          }
        }
        ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição do Serviço</th>
                        <th>Filial</th>
                        <th>Nome do Mecânico</th>
                        <th>Carro</th>
                        <th>Cor</th>
                        <th>Placa</th>
                        <th>Tipo de Combustível</th>
                        <th>Urgência</th>
                        <th>Alterar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Código</th>
                        <th>Descrição do Serviço</th>
                        <th>Filial</th>
                        <th>Nome do Mecânico</th>
                        <th>Carro</th>
                        <th>Cor</th>
                        <th>Placa</th>
                        <th>Tipo de Combustível</th>
                        <th>Urgência</th>
                        <th>Alterar</th>
                        <th>Excluir</th>
                    </tr>
                </tfoot>
                <tbody>
                <?php
                foreach($servicos as $servico) {
                    echo "<tr>";
                    echo "<td>".$servico->idServico."</td>";
                    echo "<td>".$servico->descricaoServico."</td>";
                    echo "<td>".$servico->filial."</td>";
                    echo "<td>".$servico->nomeMecanico."</td>";
                    echo "<td>".$servico->carro."</td>";
                    echo "<td>".$servico->cor."</td>";
                    echo "<td>".$servico->placa."</td>";
                    echo "<td>".$servico->tipoCombustivel."</td>";
                    echo "<td>".$servico->urgencia."</td>";
                    echo "<td><a href='alterar.php?id=$servico->idServico' class='btn btn-warning'>Alterar</a></td>";
                    echo "<td><a href='buscar.php?id=$servico->idServico' class='btn btn-danger'>Excluir</a></td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </body>
    <?php
    if(isset($_GET['id'])) {
        $servicoDAO = new servicoDAO;
        $servicoDAO->deletarServico($_GET['id']);
    }
    ?>
</html>