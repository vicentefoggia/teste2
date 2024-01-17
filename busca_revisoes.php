<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "revisoes_agendamento";

// Conectando ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Recebe a data enviada pela requisição AJAX
$dateToSearch = $conn->real_escape_string($_POST['dateToSearch']); // Sanitize user input

// Consulta SQL para buscar revisões na data fornecida
$sql = "SELECT * FROM datas_revisao WHERE data_revisao = '$dateToSearch'";
$result = $conn->query($sql);

// Verifica se há resultados
if ($result === false) {
    die("Erro na consulta SQL: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Exibe a disciplina e o conteúdo da revisão
        echo '<div class="task">';
        echo '<strong>' . $row['nome_disciplina'] . '</strong>';
        echo '<p>' . $row['conteudo_revisao'] . '</p>';
        echo '</div>';
    }
} else {
    echo '<div class="task">Sem revisões :)</div>';
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
