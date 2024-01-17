<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planner de Revisões Semanais</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f0f0f0;
        display: flex;
    }

    .sidebar {
        background-color: #2c3e50; /* Cor de fundo da barra lateral */
        color: #ecf0f1; /* Cor do texto na barra lateral */
        padding: 20px;
        width: 10%; /* Largura da barra lateral */
        height: 700px; /* Altura desejada da barra lateral */
        transition: width 0.3s; /* Animação da largura */
        overflow: hidden;
    }

    .sidebar h2 {
        margin-bottom: 20px;
        font-size: 24px;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        color: #ecf0f1;
        text-decoration: none;
        transition: background-color 0.3s; /* Animação da cor de fundo ao passar o mouse */
    }

    .sidebar a:hover {
        background-color: #34495e; /* Cor de fundo ao passar o mouse */
    }

    .separator {
        width: 1px;
        height: 100%;
        background-color: #ddd; /* Cor da linha de separação */
        margin-right: 25px; /* Ajuste o valor da margem à direita conforme necessário */
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        flex: 1;
    }

    .header {
        background-color: #3498db;
        color: #fff;
        text-align: center;
        padding: 20px;
        font-size: 20px;
    }

    .days {
        display: flex;
    }

    .day {
        flex: 1;
        padding: 20px;
        text-align: center;
        border-right: 1px solid #ccc;
    }

    .tasks {
        padding: 20px;
    }

    .task {
        margin-bottom: 10px;
    }

    .task input {
        margin-right: 10px;
    }
</style>


<script>
  document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector('.days');
    const revisionsMap = {};

    for (let i = 0; i < 7; i++) {
      const currentDay = new Date();
      const dayIndex = (currentDay.getDay() + i) % 7;
      const nextDay = new Date(currentDay);
      nextDay.setDate(currentDay.getDate() + (dayIndex - currentDay.getDay() + 7) % 7);

      const formattedDate = formatDate(nextDay);

      const dayDiv = document.createElement('div');
      dayDiv.className = 'day';
      dayDiv.innerHTML = `<div>${capitalizeFirstLetter(nextDay.toLocaleDateString('pt-BR', { weekday: 'long' }))}</div>`; // Nome do dia
      dayDiv.innerHTML += `<div>${formattedDate}</div>`; // Data
      dayDiv.innerHTML += `<div class="tasks"></div>`; // Container para as tarefas
      container.appendChild(dayDiv);

      searchRevisions(formattedDate, dayDiv);
    }

    function formatDate(date) {
      return `${padZero(date.getDate())}/${padZero(date.getMonth() + 1)}/${date.getFullYear()}`;
    }

    function searchRevisions(dateToSearch, dayDiv) {
      const xhr = new XMLHttpRequest();

      xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
          const response = xhr.responseText;
          revisionsMap[dateToSearch] = response;
          updateTasksContainer(dayDiv, dateToSearch);
        }
      };

      xhr.open("POST", "busca_revisoes.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send("dateToSearch=" + dateToSearch);
    }

    function updateTasksContainer(dayDiv, dateToSearch) {
      const tasksContainer = dayDiv.querySelector('.tasks');

      if (revisionsMap[dateToSearch]) {
        tasksContainer.innerHTML += `<div class="task">
          <div class="discipline">${revisionsMap[dateToSearch]}</div>
        </div>`;
      }
    }

    function padZero(value) {
      return value < 10 ? '0' + value : value;
    }

    function capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }
  });
</script>



</head>

<body>

  <div class="sidebar">
    <h2>Barra Lateral</h2>
    <a href="planner.php">Planner Semanal</a>
    <a href="agendamento.revisao.html">Agendar Revisões</a>
  </div>

  <div class="container">
    <div class="header">Planner de Revisões Semanais</div>
    <div class="days"></div>
    <div class="tasks">
      <!-- Adicione suas tarefas conforme necessário -->
    </div>
  </div>

</body>

</html>
