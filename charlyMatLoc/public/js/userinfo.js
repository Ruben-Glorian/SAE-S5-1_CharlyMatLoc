(function () {
    const userInfoBar = document.getElementById('userInfoBar');
    const authButton = document.getElementById('authButton');
    const email = localStorage.getItem('user_email');
    const token = localStorage.getItem('access_token');

    //si connecté
    if (email && token) {
        if (userInfoBar) {
            userInfoBar.textContent = `Connecté en tant que : ${email}`;
            userInfoBar.style.display = 'block';
        }

        if (authButton) {
            authButton.textContent = 'Déconnexion';
            authButton.onclick = () => {
                localStorage.removeItem('user_email');
                localStorage.removeItem('access_token');
                window.location.href = 'signin.html';
            };
        }
    } else {
        //si pas connecté
        if (authButton) {
            authButton.textContent = 'Connexion';
            authButton.onclick = () => {
                window.location.href = 'signin.html';
            };
        }
    }
})();