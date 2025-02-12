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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode([
            'status' => 'error',
            'message' => 'Método não permitido.'
        ]);
        exit;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $task_content = $data['task'] ?? '';
    $task_fav = $data['favorited'] ?? false;

    if (empty($task_content)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode([
            'status' => 'error',
            'message' => 'A tarefa não pode ser vazia.'
        ]);
        exit;
    }

    $tasks = getTasks();

    $task_found = false;
    foreach ($tasks as $t) {
        if ($t['task'] === $task_content) {
            $task_found = true;
            header('HTTP/1.1 409 Conflict');
            echo json_encode([
                'status' => 'error',
                'message' => 'Tarefa já existe.'
            ]);
            exit;
        }
    }

    if (!$task_found) {
        $new_task = [
            'id' => uniqid(),
            'task' => $task_content,
            'favorited' => $task_fav,
            'created_at' => date('Y-m-d H:i:s', strtotime('now'))
        ];
        
        $tasks[] = $new_task;
        saveTasks($tasks);
        
        header('HTTP/1.1 201 Created');
        echo json_encode([
            'status' => 'success',
            'message' => 'Tarefa cadastrada!',
            'task' => $new_task
        ]);
    }
?>