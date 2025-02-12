const task_input = document.getElementById("task-create");
const save = document.getElementById("save-button");
const task_list = document.getElementById("task-list");

task_input.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    createTask();
  }
});

save.addEventListener('click', () => createTask())

async function createTask() {
  const { value } = task_input;
  if (value.trim() === "" || value.length < 3) {
    renderError('Digite uma tarefa válida.');
    return;
  };
  const data = {
    task: value,
    favorited: false
  };
  saveTask(data);
};

async function getTasks() {
  try {
    const res = await fetch('api/get-tasks.php').then(r => r.json());
    return res.tasks;
  } catch (err) {
    renderError('Erro ao buscar tarefas');
    console.error('Erro:', err)
  }
}

async function updateTask(id, task, favorited) {
  try {
    const res = await fetch('api/update-task.php', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id, task, favorited })
    }).then(r => r.json());
    console.log(res);
  } catch (err) {
    renderError('Erro ao atualizar tarefa: ' + err.message);
    console.error('Erro:', err)
  } finally {
    renderTasks();
  }
}


async function deleteTask(id) {
  try {
    const res = await fetch('api/delete-task.php', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    }).then(r => r.json());

    console.log(res);
  } catch (err) {
    renderError('Erro ao deletar tarefa');
    console.error('Erro:', err)
  } finally {
    renderTasks();
  }
}

async function saveTask(data) {
  try {
    const res = await fetch('api/create-task.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    }).then((r) => r.json());

    if (res.status === 'success') {
      task_input.value = '';
    }
    if (res.status === 'error') {
      renderError(res.message);
    }
    console.log(res);
  } catch (err) {
    renderError('Erro ao salvar tarefa: ' + err.message);
    console.error('Erro:', err)
  } finally {
    renderTasks();
  }
}


async function renderTasks() {
  const tasks = await getTasks();
  tasks.sort((a, b) => {
    if (a.favorited && !b.favorited) return -1;
    if (!a.favorited && b.favorited) return 1;
    return 0;
  });
  task_list.innerHTML = '';
  tasks.forEach(task => {
    const li = document.createElement('li');
    li.innerHTML = `
      <div class="task">
        <button class="btn favorite">
          <i style="color: ${task.favorited ? 'yellow' : 'white'};" class="fas fa-star"></i>
        </button>
        <span>${task.task}</span>
        <button class="btn delete">
          <i style="color: white" class="fas fa-trash"></i>
        </button>
        <button class="btn edit">
          <i style="color: white" class="fas fa-edit"></i>
        </button>
      </div>
    `;
    li.querySelector('.favorite').addEventListener('click', (e) => {
      updateTask(task.id, task.task, !task.favorited);
    });
    li.querySelector('.delete').addEventListener('click', () => {
      deleteTask(task.id).then(() => renderTasks());
    });
    li.querySelector('.edit').addEventListener('click', () => {
      const newTask = prompt('Digite a nova tarefa', task.task);
      if (newTask === null || newTask.trim() === '') return;
      updateTask(task.id, newTask, task.favorited);
    });
    task_list.appendChild(li);
  });
}

function renderError(m) {
  const message = document.createElement('p');
  message.innerText = m;
  task_input.insertAdjacentElement('afterend', message);
  setTimeout(() => {
    message.remove();
  }, 1500);
}

renderTasks();