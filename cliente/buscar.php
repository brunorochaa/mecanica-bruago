<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Buscar Dados do Cliente</title>
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

                    <li class="nav-item active"><a class="nav-link" href="buscar.php">BUSCAR DADOS<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../servico/cadastro.php">CADASTRAR SERVIÇO<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="../produto/index.php">NOSSOS PRODUTOS<span class="sr-only"></span></a></li>
                </ul>
            </div>
        </nav>
        <h1 class="jumbotron titulo">Buscar Dados de Clientes</h1>
        <?php
        include 'dao/clientedao.class.php';
        include 'model/cliente.class.php';

        $clienteDAO = new clienteDAO;
        $clientes = $clienteDAO->buscarCliente();

        if(count($clientes) == 0) {
            echo "<h1>Não há clientes no banco!</h1>";
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
                <option value="nome">Nome</option>
                <option value="sexo">Sexo</option>
                <option value="dataNascimendo">Data de Nascimento</option>
                <option value="cep">CEP</option>
                <option value="numeroCasa">Número de Casa</option>
                <option value="telefone">Telefone</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary btn-block">
          </div>
        </form>
        <?php
        if(isset($_POST['filtrar'])){
          include_once 'dao/clientedao.class.php';
          $pesquisa = $_POST['pesquisa'];
          $filtro = $_POST['filtro'];
          $clienteDAO = new ClienteDAO;
          $clientes = $clienteDAO->filtrarCliente($filtro, $pesquisa);

          if(count($clientes) == 0){
            echo "<h2>Sua pesquisa não retornou nenhum cliente!</h2>";
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
                        <th>Nome</th>
                        <th>Sexo</th>
                        <th>Data de Nascimento</th>
                        <th>CEP</th>
                        <th>Número de Casa</th>
                        <th>Telefone</th>
                        <th>Alterar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Sexo</th>
                        <th>Data de Nascimento</th>
                        <th>CEP</th>
                        <th>Número de Casa</th>
                        <th>Telefone</th>
                        <th>Alterar</th>
                        <th>Excluir</th>
                    </tr>
                </tfoot>
                <tbody>
                <?php
                foreach($clientes as $cliente) {
                    echo "<tr>";
                    echo "<td>".$cliente->idCliente."</td>";
                    echo "<td>".$cliente->nome."</td>";
                    echo "<td>".$cliente->sexo."</td>";
                    echo "<td>".$cliente->dataNascimento."</td>";
                    echo "<td>".$cliente->cep."</td>";
                    echo "<td>".$cliente->numeroCasa."</td>";
                    echo "<td>".$cliente->telefone."</td>";
                    echo "<td><a href='alterar.php?id=$cliente->idCliente' class='btn btn-warning'>Alterar</a></td>";
                    echo "<td><a href='buscar.php?id=$cliente->idCliente' class='btn btn-danger'>Excluir</a></td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </body>
    <?php
    if(isset($_GET['id'])) {
        $clienteDAO = new clienteDAO;
        $clienteDAO->deletarCliente($_GET['id']);
    }
    ?>
</html>