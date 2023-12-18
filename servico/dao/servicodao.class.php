<?php
require "conexaobanco.class.php";
class ServicoDAO
{
    private $conexao = null;

    public function __construct()
    {
        $this->conexao = ConexaoBanco::getInstance();
    }

    public function __destruct()
    {
    }

    public function cadastrar($servico)
    {
        try {
            $statement = $this->conexao->prepare("insert into servico(idServico,descricaoServico,filial,nomeMecanico,carro,cor,placa,tipoCombustivel,urgencia)values(null,?,?,?,?,?,?,?,?)");
            $statement->bindValue(1, $servico->descricaoServico);
            $statement->bindValue(2, $servico->filial);
            $statement->bindValue(3, $servico->nomeMecanico);
            $statement->bindValue(4, $servico->carro);
            $statement->bindValue(5, $servico->cor);
            $statement->bindValue(6, $servico->placa);
            $statement->bindValue(7, $servico->tipoCombustivel);
            $statement->bindValue(8, $servico->urgencia);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao cadastrar!".$error;
        }
    }

    public function buscarServico()
    {
        try {
            $statement = $this->conexao->query("select * from servico");
            $array = $statement->fetchAll(PDO::FETCH_CLASS,"Servico");
            return $array;
        } catch(PDOException $error) {
            echo "Erro ao buscar! ".$error;
        }
    }

    public function deletarServico($idServico)
    {
        try {
            $statement = $this->conexao->prepare("delete from servico where idServico = ?");
            $statement->bindValue(1, $idServico);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao excluir! ".$error;
        }
    }

    public function filtrarServico($filtro, $pesquisa)
    {
        try {
            $query = "";
            switch($filtro) {
                case "codigo": $query = "where idServico = ".$pesquisa;
                break;
                case "descricaoServico": $query = "where descricaoServico like '%{$pesquisa}%'";
                break;
                case "filial": $query = "where filial like '%{$pesquisa}%'";
                break;
                case "nomeMecanico": $query = "where nomeMecanico like '%{$pesquisa}%'";
                break;
                case "carro": $query = "where carro like '%{$pesquisa}%'";
                break;
                case "cor": $query = "where cor like '%{$pesquisa}%'";
                break;
                case "placa": $query = "where placa like '%{$pesquisa}%'";
                break;
                case "tipoCombustivel": $query = "where tipoCombustivel like '%{$pesquisa}%'";
                break;
                case "urgencia": $query = "where urgencia like '%{$pesquisa}%'";
                break;
                case "todos": $query = "";
                break;
                case "default": $query = "";
                break;
            }
            $statement = $this->conexao->query("select * from servico {$query}");
            $array = $statement->fetchAll(PDO::FETCH_CLASS,"Servico");
            return $array;
        } catch(PDOException $error) {
            echo "Erro ao filtrar! ".$error;
        }
    }

    public function alterarServico($servico)
    {
        try {
            $statement = $this->conexao->prepare("update servico set descricaoServico=?, filial=?, nomeMecanico=?, carro=?, cor=?, placa=?, tipoCombustivel=?, urgencia=? where idServico=?");
            $statement->bindValue(1, $servico->descricaoServico);
            $statement->bindValue(2, $servico->filial);
            $statement->bindValue(3, $servico->nomeMecanico);
            $statement->bindValue(4, $servico->carro);
            $statement->bindValue(5, $servico->cor);
            $statement->bindValue(6, $servico->placa);
            $statement->bindValue(7, $servico->tipoCombustivel);
            $statement->bindValue(8, $servico->urgencia);
            $statement->bindValue(9, $servico->idServico);
            $statement->execute();
        } catch(PDOException $error) {
            echo "Erro ao alterar! ".$error;
        }
    }
}