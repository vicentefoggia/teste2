<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $disciplina = $_POST["disciplina_nome"];
    $dataEstudo = $_POST["data_estudo"];
    $conteudoEstudado = $_POST["conteudo_estudado"];
    $descricaoEstudo = $_POST["descricao_estudo"];

    // Validação dos dados (pode ser mais robusta dependendo dos requisitos)
    if (empty($disciplina) || empty($dataEstudo) || empty($conteudoEstudado) || empty($descricaoEstudo)) {
        echo "Todos os campos são obrigatórios.";
        exit();
    }

    // Formata a data de estudo para o padrão DD/MM/YYYY
    $dataEstudoFormatada = date('d/m/Y', strtotime($dataEstudo));

    // Configurações do banco de dados (substitua com suas próprias configurações)
    $host = "localhost";
    $usuario = "root";
    $senha = "root";
    $banco = "revisoes_agendamento";

    try {
        // Conecta ao banco de dados
        $conexao = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Insere os dados na tabela agendamentos
        $sqlAgendamento = "INSERT INTO agendamentos (disciplina_nome, data_estudo, conteudo_estudado, descricao_estudo)
                           VALUES (:disciplina, :dataEstudo, :conteudoEstudado, :descricaoEstudo)";
        $stmtAgendamento = $conexao->prepare($sqlAgendamento);
        $stmtAgendamento->bindParam(':disciplina', $disciplina);
        $stmtAgendamento->bindParam(':dataEstudo', $dataEstudoFormatada); // Use a data formatada aqui
        $stmtAgendamento->bindParam(':conteudoEstudado', $conteudoEstudado);
        $stmtAgendamento->bindParam(':descricaoEstudo', $descricaoEstudo);
        $stmtAgendamento->execute();

        // Insere as datas de revisão na tabela datas_revisao
        $idAgendamento = $conexao->lastInsertId();
        $intervalos = array(7, 14, 28, 56, 112, 168, 224, 280, 336);

        foreach ($intervalos as $intervalo) {
            $dataRevisao = date('d/m/Y', strtotime($dataEstudo . " + $intervalo days")); // Formatação para DD/MM/YYYY
            $sqlRevisao = "INSERT INTO datas_revisao (id_agendamento, data_revisao, nome_disciplina, conteudo_revisao)
                           VALUES (?, ?, ?, ?)";
            $stmtRevisao = $conexao->prepare($sqlRevisao);
            $stmtRevisao->bindParam(1, $idAgendamento);
            $stmtRevisao->bindParam(2, $dataRevisao);
            $stmtRevisao->bindParam(3, $disciplina);
            $stmtRevisao->bindParam(4, $conteudoEstudado); // Inclui o conteúdo estudado na revisão
            $stmtRevisao->execute();
        }

        // Mensagem de sucesso
        echo "Revisão agendada com sucesso!";

        // Redireciona para a página planner.php após a mensagem de sucesso
        header("Location: planner.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro ao agendar a revisão: " . $e->getMessage();
    } finally {
        // Fecha a conexão com o banco de dados
        $conexao = null;
    }
} else {
    // Redireciona se o formulário não foi enviado
    header("Location: index.html");
    exit();
}
?>
