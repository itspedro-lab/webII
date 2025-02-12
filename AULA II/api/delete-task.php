<?php
  header('Content-Type: application/json');

  function getTasks() {
      if (!file_exists('bancodedados.json')) {
          return [];
      }
      $data = file_get_contents('bancodedados.json');
      return json_decode($data, true) ?: [];
  }

  function saveTasks($tasks) {
      file_put_contents('bancodedados.json', json_encode($tasks, JSON_PRETTY_PRINT));
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode([
        'status' => 'error',
        'message' => 'Método não permitido.'
    ]);
    exit;
}

  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  $task_id = $data['id'] ?? null;

  if (!$task_id) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode([
        'status' => 'error',
        'message' => 'ID da tarefa não informado.'
    ]);
    exit;
  }

  $tasks = getTasks();
  $task_found = false;
  $updated_tasks = [];

  foreach ($tasks as $task) {
      if ($task['id'] === $task_id) {
          $task_found = true;
      } else {
          $updated_tasks[] = $task;
      }
  }

  if ($task_found) {
      saveTasks($updated_tasks);
      echo json_encode([
          'status' => 'success',
          'message' => 'Tarefa removida!'
      ]);
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'Tarefa não encontrada'
      ]);
  }
?>