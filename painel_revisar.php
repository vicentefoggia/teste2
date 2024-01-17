<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Revisões</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            margin-right: 10px;
        }

        select, button {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4fa3d1;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<?php
// Configurações do banco de dados (substitua com suas próprias configurações)
$host = "localhost";
$usuario = "root";
$senha = "root";
$banco = "revisoes_agendamento";

// Obtém a disciplina selecionada (se existir)
$disciplinaSelecionada = isset($_GET['disciplina']) ? htmlspecialchars($_GET['disciplina']) : '';

try {
    // Define a localidade para o formato de data em português
    setlocale(LC_TIME, 'pt_BR', 'ptb');

    // Conecta ao banco de dados
    $conexao = new PDO("mysql:host=$host;dbname=$banco", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obter todas as revisões ou filtrar por disciplina
    $sql = "SELECT agendamentos.disciplina_nome, datas_revisao.data_revisao, agendamentos.conteudo_estudado, agendamentos.descricao_estudo
            FROM datas_revisao
            INNER JOIN agendamentos ON datas_revisao.id_agendamento = agendamentos.id";

    // Adiciona a cláusula WHERE para filtrar por disciplina, se selecionada
    if ($disciplinaSelecionada) {
        $sql .= " WHERE agendamentos.disciplina_nome = :disciplina";
    }

    // Adiciona a cláusula ORDER BY para ordenar por data de revisão
    $sql .= " ORDER BY datas_revisao.data_revisao";

    // Prepara a consulta SQL
    $stmt = $conexao->prepare($sql);

    // Define o parâmetro da disciplina, se selecionada
    if ($disciplinaSelecionada) {
        $stmt->bindParam(':disciplina', $disciplinaSelecionada);
    }

    // Executa a consulta SQL
    $stmt->execute();

    // Exibe os resultados
    echo "<h2>Lista de Revisões</h2>";

    // Adiciona botões para filtrar por disciplina
    echo "<form action='' method='get'>";
    echo "<label for='disciplina'>Filtrar por disciplina:</label>";
    echo "<select id='disciplina' name='disciplina'>";
    echo "<option value=''>Todas as disciplinas</option>";
    // Substitua com suas disciplinas reais
    $disciplinas = array('Matemática', 'Física', 'Geografia');
    foreach ($disciplinas as $disciplina) {
        $selected = ($disciplina == $disciplinaSelecionada) ? 'selected' : '';
        echo "<option value='$disciplina' $selected>$disciplina</option>";
    }
    echo "</select>";
    echo "<button type='submit'>Filtrar</button>";
    echo "</form>";

    echo "<table border='1'>";
    echo "<tr><th>Disciplina</th><th>Data de Revisão</th><th>Conteúdo Estudado</th><th>Descrição do Estudo</th></tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Converte a data para o formato brasileiro com o mês por extenso
        $dataFormatada = strftime('%d de %B de %Y', strtotime($row['data_revisao']));

        echo "<tr>";
        echo "<td>{$row['disciplina_nome']}</td>";
        echo "<td>{$dataFormatada}</td>";
        echo "<td>{$row['conteudo_estudado']}</td>";
        echo "<td>{$row['descricao_estudo']}</td>";
        echo "</tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    echo "Erro ao recuperar as revisões: " . $e->getMessage();
} finally {
    // Fecha a conexão com o banco de dados
    $conexao = null;
}
?>
</body>
</html>
