const form = document.getElementById("form-login");


addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    Login(data);
});

async function Login(data) {
    try {
        const res = await fetch('/api.php', {
            method: 'POST',
            body: data
        }).then((r) => r.json());
        console.log(res);
    } catch (err) {
        console.error('Erro:', err)
    }
}