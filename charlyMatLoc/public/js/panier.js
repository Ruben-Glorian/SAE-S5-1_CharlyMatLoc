//recup le panier depuis l'api et affiche dynamiquement les cartes outils
function loadPanier() {
    const token = localStorage.getItem('access_token');
    if (!token) {
        window.location.href = 'signin.html';
        return;
    }

    fetch('/api/panier', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
        .then(res => res.json())
    .then(data => {
        //selectionne le conteneur du panier
        const panierDiv = document.getElementById('panier');
        const totalDiv = document.getElementById('total');
        const validateBtn = document.getElementById('valider_panier');
        const messageDiv = document.getElementById('panier-message');
        //vide le conteneur avant d'ajouter les nouveaux elems
        panierDiv.innerHTML = '';
        messageDiv.textContent = '';
        //utilise le tableau d'outils retourné par l'api
        const outils = data.panier || [];
        if (outils.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Votre panier est vide.';
            panierDiv.appendChild(empty);
            totalDiv.textContent = '';
            //masquer le bouton valider vu que c'est vide
            validateBtn.style.display = 'none';
            return;
        }
        //crée une carte et l'ajoute au panier pour chaque outil du panier
        outils.forEach(outil => {
            const card = document.createElement('div');
            card.className = 'card';

            //img de l'outils
            const img = document.createElement('img');
            img.src = outil.image_url;
            img.alt = outil.nom;
            card.appendChild(img);

            //nom
            const name = document.createElement('div');
            name.className = 'name';
            name.textContent = outil.nom;
            card.appendChild(name);

            //date de location
            const meta = document.createElement('div');
            meta.className = 'meta';
            meta.textContent = `Location le ${outil.date_location}`;
            card.appendChild(meta);

            //tarif
            const tarif = document.createElement('div');
            tarif.className = 'meta';
            tarif.textContent = `${outil.tarif} €`;
            card.appendChild(tarif);

            panierDiv.appendChild(card);        });
        //total du panier
        totalDiv.textContent = `Montant total : ${data.total.toFixed(2)} €`;
        //bouton valider
        validateBtn.style.display = 'inline-block';
    }).catch(err => {
        console.error('Erreur chargement panier', err);
    });
}

//Handler pour valider le panier
function validatePanier() {
    const token = localStorage.getItem('access_token');
    const messageDiv = document.getElementById('panier-message');
    if (!token) {
        window.location.href = 'signin.html';
        return;
    }
    messageDiv.textContent = 'Validation en cours...';
    fetch('/api/panier/valider', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        }
    }).then(async res => {
        const body = await res.json().catch(() => ({}));
        if (res.ok) {
            messageDiv.textContent = body.message || 'Panier validé.';
            //Recharger le panier lorsqu'il est validé
            loadPanier();
        } else {
            messageDiv.textContent = body.error || `Erreur serveur (${res.status})`;
        }
    }).catch(err => {
        console.error('Erreur lors de la validation', err);
        messageDiv.textContent = 'Erreur réseau lors de la validation.';
    });
}

//Initialiser au chargement de la page
window.addEventListener('DOMContentLoaded', () => {
    loadPanier();
    const validateBtn = document.getElementById('valider_panier');
    validateBtn.addEventListener('click', (e) => {
        e.preventDefault();
        validatePanier();
    });
});