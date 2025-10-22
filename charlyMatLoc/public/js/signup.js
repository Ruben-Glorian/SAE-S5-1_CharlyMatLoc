document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('signupError');
    errorDiv.textContent = '';
    try {
        const res = await fetch('/signup', {
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
        //stock l'email pour pré remplir le formulaire de connexion
        localStorage.setItem('user_email', data.profile.email);
        //redirige vers la page de connexion
        window.location.href = 'signin.html';
    } catch (err) {
        errorDiv.textContent = 'Erreur lors de l\'inscription.';
    }
});