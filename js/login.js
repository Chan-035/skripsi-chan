document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    fetch('js/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Login berhasil, mengarahkan ke dashboard...');
            window.location.href = 'dashboard.php';
        } else {
            document.getElementById('loginMessage').textContent = 'Login gagal!';
            document.getElementById('loginMessage').style.color = 'red';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loginMessage').textContent = 'Terjadi kesalahan saat login.';
        document.getElementById('loginMessage').style.color = 'red';
    });
});