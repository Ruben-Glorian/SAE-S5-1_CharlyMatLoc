document.getElementById('signinForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('signinError');
    errorDiv.textContent = '';
    try {
        const res = await fetch('/signin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();
        if (!res.ok || data.error) {
            errorDiv.textContent = data.error || 'Erreur de connexion.';
            return;
        }
        //stock le token JWT et l'email utilisateur
        localStorage.setItem('access_token', data.token);
        localStorage.setItem('user_email', data.profile.email);
        //redirige vers le catalogue
        window.location.href = 'catalogue.html';
    } catch (err) {
        errorDiv.textContent = 'Erreur de connexion.';
    }
});