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
        if (!res.ok) {
            const err = await res.text();
            errorDiv.textContent = err;
            return;
        }
        const data = await res.json();
        //stock le token dans le localStorage pour le panier utilisateur
        localStorage.setItem('access_token', data.payload.access_token);
        localStorage.setItem('user_email', data.profile.email);
        //redirige vers le catalogue ou le panier
        window.location.href = 'catalogue.html';
    } catch (err) {
        errorDiv.textContent = 'Erreur de connexion.';
    }
});