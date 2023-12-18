<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Mecânica</title>
        <link href="css/style.css" rel="stylesheet">
        <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <script src="node_modules/jquery/dist/jquery.min.js"></script>
        <script src="node_modules/ajax/lib/ajax.js"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="index.php">Mecânica</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active"><a class="nav-link" href="index.php">HOME</a></li>

                    <li class="nav-item"><a class="nav-link" href="cliente/cadastro.php">CADASTRAR CLIENTE<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="servico/cadastro.php">CADASTRAR SERVIÇO<span class="sr-only"></span></a></li>

                    <li class="nav-item"><a class="nav-link" href="produto/index.php">NOSSOS PRODUTOS<span class="sr-only"></span></a></li>
                </ul>
            </div>
        </nav>
        <h1 class="jumbotron bg-grey titulo">Seja bem vindo</h1>
        <ul>
            <li><a href="cliente/cadastro.php">CADASTRAR CLIENTE</a></li>
            <li><a href="servico/cadastro.php">CADASTRAR SERVIÇO</a></li>
            <li><a href="produto/index.php">CONHEÇA NOSSA MARCA</a></li>
        </ul>
    </body>
</html>