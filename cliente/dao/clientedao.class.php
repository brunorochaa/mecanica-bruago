<?php
require "conexaobanco.class.php";
class ClienteDAO
{
    private $conexao = null;

    public function __construct()
    {
        $this->conexao = ConexaoBanco::getInstance();
    }

    public function __destruct()
    {
    }

    public function cadastrar($cliente)
    {
        try {
            $statement = $this->conexao->prepare("insert into cliente(idCliente,nome,sexo,dataNascimento,cep,numeroCasa,telefone)values(null,?,?,?,?,?,?)");
            $statement->bindValue(1, $cliente->nome);
            $statement->bindValue(2, $cliente->sexo);
            $statement->bindValue(3, $cliente->dataNascimento);
            $statement->bindValue(4, $cliente->cep);
            $statement->bindValue(5, $cliente->numeroCasa);
            $statement->bindValue(6, $cliente->telefone);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao cadastrar!".$error;
        }
    }

    public function buscarCliente()
    {
        try {
            $statement = $this->conexao->query("select * from cliente");
            $array = $statement->fetchAll(PDO::FETCH_CLASS,"Cliente");
            return $array;
        } catch(PDOException $error) {
            echo "Erro ao buscar! ".$error;
        }
    }

    public function deletarCliente($idCliente)
    {
        try {
            $statement = $this->conexao->prepare("delete from cliente where idCliente = ?");
            $statement->bindValue(1, $idCliente);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao excluir! ".$error;
        }
    }

    public function filtrarCliente($filtro, $pesquisa)
    {
        try {
            $query = "";
            switch($filtro) {
                case "codigo": $query = "where idCliente = ".$pesquisa;
                break;
                case "nome": $query = "where nome like '%{$pesquisa}%'";
                break;
                case "sexo": $query = "where sexo like '%{$pesquisa}%'";
                break;
                case "dataNascimento": $query = "where dataNascimento like '%{$pesquisa}%'";
                break;
                case "cep": $query = "where cep like '%{$pesquisa}%'";
                break;
                case "numeroCasa": $query = "where numeroCasa like '%{$pesquisa}%'";
                break;
                case "telefone": $query = "where telefone like '%{$pesquisa}%'";
                break;
                case "todos": $query = "";
                break;
                case "default": $query = "";
                break;
            }
            $statement = $this->conexao->query("select * from cliente {$query}");
            $array = $statement->fetchAll(PDO::FETCH_CLASS,"Cliente");
            return $array;
        } catch(PDOException $error) {
            echo "Erro ao filtrar! ".$error;
        }
    }

    public function alterarCliente($cliente)
    {
        try {
            $statement = $this->conexao->prepare("update cliente set nome=?, sexo=?, dataNascimento=?, cep=?, numeroCasa=?, telefone=? where idCliente=?");
            $statement->bindValue(1, $cliente->nome);
            $statement->bindValue(2, $cliente->sexo);
            $statement->bindValue(3, $cliente->dataNascimento);
            $statement->bindValue(4, $cliente->cep);
            $statement->bindValue(5, $cliente->numeroCasa);
            $statement->bindValue(6, $cliente->telefone);
            $statement->bindValue(7, $cliente->idCliente);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao alterar! ".$error;
        }
    }
}