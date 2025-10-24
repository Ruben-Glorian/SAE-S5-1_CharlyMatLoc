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
            const id = outil.outil_id ?? '';
            const outil_existant = panierDiv.querySelector(`.card[data-id="${id}"]`);
            if(outil_existant){
                let ul = outil_existant.querySelector('.dates-list');
                if (!ul) {
                    ul = document.createElement('ul');
                    ul.className = 'dates-list';
                    outil_existant.insertBefore(ul, outil_existant.querySelector('.meta') || null);
                }
                const li = document.createElement('li');
                li.textContent = outil.date_location || '—';
                ul.appendChild(li);
                let badge = outil_existant.querySelector('.badge');
                let count = badge ? parseInt(badge.dataset.count || '1', 10) + 1 : 2;
                if (!badge) {
                    badge = document.createElement('div');
                    badge.className = 'badge';
                    outil_existant.insertBefore(badge, outil_existant.querySelector('.meta') || null);
                }
                badge.dataset.count = String(count);
                badge.textContent = `${count} réservations`;
                const tarifDiv = outil_existant.querySelector('.tarif');
                const unit = outil.tarif;
                if (tarifDiv) {
                    tarifDiv.textContent = `${unit.toFixed(2)} € chacun — Sous‑total : ${(unit * count).toFixed(2)} €`;
                }

            }
            else{
                const card = document.createElement('div');
                card.className = 'card';
                card.setAttribute('data-id', id);

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

                //liste de dates si outil réservé plusieurs fois
                const ul = document.createElement('ul');
                ul.className = 'dates-list';
                const li = document.createElement('li');
                li.textContent = outil.date_location || '—';
                ul.appendChild(li);
                card.appendChild(ul);

                //tarif
                const tarif = document.createElement('div');
                tarif.className = 'meta tarif';
                tarif.textContent = outil.tarif+'€';
                card.appendChild(tarif);

                panierDiv.appendChild(card);        }
            });
        //total du panier
        totalDiv.textContent = `Montant total : ${data.total} €`;
        //bouton valider
        validateBtn.style.display = 'inline-block';
    }).catch(err => {
        console.error('Erreur chargement panier', err);
    });
}

//handler pour valider le panier
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

//initialiser au chargement de la page
window.addEventListener('DOMContentLoaded', () => {
    loadPanier();
    const validateBtn = document.getElementById('valider_panier');
    validateBtn.addEventListener('click', (e) => {
        e.preventDefault();
        validatePanier();
    });
});