<?php
  header('Content-Type: application/json');

  function getTasks() {
      if (!file_exists('bancodedados.json')) {
          return [];
      }
      $data = file_get_contents('bancodedados.json');
      return json_decode($data, true) ?: [];
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode([
        'status' => 'error',
        'message' => 'Método não permitido.'
    ]);
    exit;
}

  $tasks = getTasks();

  header('HTTP/1.1 200 OK');
  echo json_encode([
      'status' => 'success',
      'tasks' => $tasks
  ]);
?>