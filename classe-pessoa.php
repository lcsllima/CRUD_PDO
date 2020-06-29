<?php

class Pessoa{
    //Como iremos usar a $pdo em todos lugares, iremos instanciar aqui
    private $pdo;

    // a primeira função é sempre executada no construct
    public function __construct($dbname, $host, $usuario, $senha)
    {
        try{
        //                  primeiro parâmetro              segundo         terceiro
        $this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host, $usuario,$senha);
        }
        // esse é para os erros do PDO
        catch (Exception $e) {
            echo "Erro com banco de dados: ".$e->getMessage();
            // para o código após os erros.
            exit();
        }
        catch (Exception $e){
            echo "Erro genérico: " .$e->getMessage();
        }
        
    }
    // FUNCAO PARA BUSCAR DADOS E COLOCAR NA TELA DIREITAS
    public function buscarDados(){
        $res = array(); // caso o BD esteja vazio, assim não da erro, volta um array vazio.
        // aqui temos a requisição, as informações vão ao $cmd
        $cmd = $this->pdo->query("SELECT * FROM pessoa ORDER BY nome");
        // aqui temos a requisição, as informações vão ao $cmd
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function cadastrarPessoa($nome, $telefone, $email){
        //ANTES DE CADASTRAR VERIFICAR SE JA TEM O EMAIL CADASTRADO
        $cmd = $this->pdo->prepare("SELECT id from pessoa WHERE email = :e");
        $cmd->bindValue(":e", $email);
        $cmd->execute();
        //Email ja existe no banco
        if($cmd->rowCount() > 0){
            return false;
        }else //não foi encontrado o email
        {
            $cmd = $this->pdo->prepare("INSERT INTO pessoa (nome, telefone, email) VALUES (:n, :t, :e)");
            $cmd->bindValue(":n", $nome);
            $cmd->bindValue(":t", $telefone);
            $cmd->bindValue(":e", $email);
            $cmd->execute();
            return true;
        }
    }
    public function excluirPessoa($id){
        $cmd = $this->pdo->prepare("DELETE FROM pessoa WHERE id = :id");

        $cmd->bindValue(":id", $id);
        $cmd->execute();
    }
    // BUSCAR DADOS DE UMA PESSOA
    public function buscarDadosPessoas($id){
        $res = array();
        $cmd = $this->pdo->prepare("SELECT * FROM pessoa WHERE id= :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
        // é só um array, então usamos o FETCH, se fosse vários, seria fetchAll
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    // ATUALIZAR DADOS NO BANCO DE DADOS
    public function atualizarDados($id, $nome, $telefone, $email){
        // antes de atualizar, verificar se email ja esta cadastrado
        $cmd = $this->pdo->prepare("UPDATE pessoa SET nome = :n, telefone = :t, email = :e WHERE id = :id");
        $cmd->bindValue(":n", $nome);
        $cmd->bindValue(":t", $telefone);
        $cmd->bindValue(":e", $email);
        $cmd->bindValue(":id", $id);
        $cmd->execute();    
    }
}

?>
