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

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $task_content = $data['task'] ??  '';
    $task_fav = $data['favorited'] ?? false;

    $tasks = getTasks();

    $task_found = false;
    foreach ($tasks as $t) {
        if ($t['task'] === $task_content) {
            $task_found = true;
            echo json_encode([
                'status' => 'error',
                'message' => 'Tarefa já existe.'
            ]);
            exit;
        }
    }

    if (!$task_found) {
        $tasks[] = [
            'id' => random_int(1, 9999),
            'task' => $task_content,
            'favorited' => $task_fav,
            'created_at' => date_create()->format('d/m/Y H:i:s')
        ];
        
        saveTasks($tasks);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Tarefa cadastrada!',
            'task' => $task_content
        ]);
    }
?>