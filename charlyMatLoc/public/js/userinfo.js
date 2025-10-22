// Affiche l'email de l'utilisateur connecté si présent dans le localStorage
(function() {
    const userInfoBar = document.getElementById('userInfoBar');
    const email = localStorage.getItem('user_email');
    if (userInfoBar && email) {
        userInfoBar.textContent = `Connecté en tant que : ${email}`;
        userInfoBar.style.display = 'block';
    }
})();

