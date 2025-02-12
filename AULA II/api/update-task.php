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

  if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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
  $task_content = $data['task'] ?? '';
  $task_fav = $data['favorited'] ?? false;

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

  foreach ($tasks as &$task) {
      if ($task['id'] === $task_id) {
          $task_found = true;
          $task['task'] = $task_content ?: $task['task'];
          $task['favorited'] = $task_fav;
          break;
      }
  }

  if ($task_found) {
      saveTasks($tasks);
      echo json_encode([
          'status' => 'success',
          'message' => 'Tarefa atualizada!',
          'task' => $task
      ]);
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'Tarefa não encontrada'
      ]);
  }
?>