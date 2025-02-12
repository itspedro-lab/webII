console.log('opa');

const task_input = document.getElementById("task-create");
const save = document.getElementById("save-button");


save.addEventListener('click', () => createTask())

async function createTask(e) {
  const { value } = task_input;
  if (value.trim() === "") return;
  const data = {
    task: value,
    favorited: false
  };
  saveTask(data);
};



async function saveTask(data) {
  try {
    console.log(data);
    const res = await fetch('/create-task.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    }).then((r) => r.json());

    if (res.status === 'success') {
      task_input.value = '';
    }
    console.log(res);
  } catch (err) {
    console.error('Erro:', err)
  }
}
