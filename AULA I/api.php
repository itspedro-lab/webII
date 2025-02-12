<?php
    header('Content-Type: application/json');

    function getUsers() {
        if (!file_exists('bancodedados.json')) {
            return [];
        }
        $data = file_get_contents('bancodedados.json');
        return json_decode($data, true) ?: [];
    }

    function saveUsers($users) {
        file_put_contents('bancodedados.json', json_encode($users, JSON_PRETTY_PRINT));
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $users = getUsers();

    $userFound = false;
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $userFound = true;
            if (password_verify($password, $user['password'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login efetuado com sucesso'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Senha incorreta'
                ]);
            }
            exit;
        }
    }

    if (!$userFound) {
        $users[] = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        
        saveUsers($users);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuário cadastrado com sucesso'
        ]);
    }
?>