function loadReservations() {
    //recup le token d'authentification stocké dans le navigateur
    const token = localStorage.getItem('access_token');
    //pas de token -> page de connexion
    if (!token) {
        window.location.href = 'signin.html';
        return;
    }

    //requête à l'api pour recup les réservations de l'utilisateur
    fetch('/api/reservation', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(res => {
        //verif le type de contenu de la réponse
        const ct = (res.headers.get('content-type') || '').toLowerCase();
        if (!res.ok) {
            return res.text().then(text => { throw {status: res.status, body: text, contentType: ct}; });
        }
        //si el contenu c'est pas du json
        if (!ct.includes('application/json')) {
            return res.text().then(text => { throw {status: res.status, body: text, contentType: ct}; });
        }
        return res.json();
    })
    .then(data => {
        //recup les éléments du dom pour afficher les réservations et les messages
        const reservationsDiv = document.getElementById('reservations');
        const messageDiv = document.getElementById('reservation-message');
        if (!reservationsDiv) return;

        //vide l'affichage précédent
        reservationsDiv.innerHTML = '';
        if (messageDiv) messageDiv.textContent = '';

        //recup la liste des réservations
        const items = data.reservations || [];
        //si aucune réservation, affiche un message
        if (!Array.isArray(items) || items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Aucune réservation trouvée.';
            reservationsDiv.appendChild(empty);
            return;
        }

        items.forEach(r => {
            const outil_id = r.outil_id;
            //cherche si une carte existe déjà pour cet outil
            let card = reservationsDiv.querySelector(`.card[data-outil="${outil_id}"]`);

            if (card) {
                //on ajoute la date à la liste des dates de réservation
                let ul = card.querySelector('.dates-list');
                if (!ul) {
                    ul = document.createElement('ul');
                    ul.className = 'dates-list';
                    card.appendChild(ul);
                }
                const li = document.createElement('li');
                li.textContent = r.date_location;
                ul.appendChild(li);

                //maj du nombre de réservations
                const count = ul.children.length;
                let badge = card.querySelector('.badge');
                if (count > 1) {
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'badge';
                        card.insertBefore(badge, card.querySelector('.meta') || null);
                    }
                    badge.textContent = `${count} réservations`;
                } else if (badge) {
                    badge.remove();
                }

                //maj tarif total
                const tarifUnit = card.dataset.tarif;
                let tarifDiv = card.querySelector('.meta.tarif');
                if (tarifUnit > 0) {
                    const text = `${tarifUnit} € chacun` + (count > 0 ? ` — Sous‑total : ${tarifUnit * count} €` : '');
                    if (!tarifDiv) {
                        tarifDiv = document.createElement('div');
                        tarifDiv.className = 'meta tarif';
                        card.appendChild(tarifDiv);
                    }
                    tarifDiv.textContent = text;
                } else if (tarifDiv) {
                    tarifDiv.remove();
                }

                return;
            }

            //nouvelle carte pour un nouvel outil
            card = document.createElement('div');
            card.className = 'card';
            card.dataset.outil = outil_id;

            const unitTarif = r.tarif;
            if (unitTarif > 0) card.dataset.tarif = String(unitTarif);

            //nom de l'outil
            const title = document.createElement('div');
            title.className = 'name';
            title.textContent = r.nom || r.outil_nom;
            card.appendChild(title);

            //img
            if (r.image_url) {
                const img = document.createElement('img');
                img.src = r.image_url;
                img.alt = r.nom || r.outil_nom || 'outil';
                card.appendChild(img);
            }

            //liste des dates de réservation
            const ul = document.createElement('ul');
            ul.className = 'dates-list';
            if (r.date_location) {
                const li = document.createElement('li');
                li.textContent = r.date_location;
                ul.appendChild(li);
            }
            card.appendChild(ul);

            //date réservation
            if (r.date_reservation) {
                const dateRes = document.createElement('div');
                dateRes.className = 'meta';
                const d = new Date(r.date_reservation);
                const timeStr = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                dateRes.textContent = `Réservé le ${timeStr}`;
                card.appendChild(dateRes);
            }

            //tarif unitaire + total
            if (unitTarif > 0) {
                const count = ul.children.length;
                const tarifDiv = document.createElement('div');
                tarifDiv.className = 'meta tarif';
                tarifDiv.textContent = `${unitTarif} € chacun` + (count > 0 ? ` — Sous‑total : ${unitTarif * count} €` : '');
                card.appendChild(tarifDiv);
            }

            //affiche un badge si plusieurs réservations pour le même outil
            const nbReservations = ul.children.length;
            if (nbReservations > 1) {
                const badge = document.createElement('div');
                badge.className = 'badge';
                badge.textContent = `${nbReservations} réservations`;
                card.insertBefore(badge, card.querySelector('.meta') || null);
            }

            reservationsDiv.appendChild(card);
        });

    })
    .catch(err => {
        //erreurs d'affichage et de recup
        console.error('Erreur chargement réservations', err);
        const reservationsDiv = document.getElementById('reservations');
        const messageDiv = document.getElementById('reservation-message');
        if (reservationsDiv) reservationsDiv.innerHTML = '';
        if (err && typeof err.body === 'string' && err.body.trim().startsWith('<')) {
            if (messageDiv) messageDiv.textContent = 'Erreur serveur (voir la console pour détails).';
            console.error('Server response (HTML):', err.body);
        } else if (err && err.body) {
            if (messageDiv) messageDiv.textContent = err.body || 'Erreur serveur.';
        } else if (err && err.status) {
            if (messageDiv) messageDiv.textContent = `Erreur serveur (${err.status}).`;
        } else {
            if (messageDiv) messageDiv.textContent = 'Erreur réseau lors du chargement des réservations.';
        }
    });
}

//initialisation au chargement de la page, crée les divs si absentes et lance le chargement
window.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('reservations')) {
        const c = document.createElement('div');
        c.id = 'reservations';
        document.body.appendChild(c);
    }
    if (!document.getElementById('reservation-message')) {
        const m = document.createElement('div');
        m.id = 'reservation-message';
        document.body.appendChild(m);
    }

    loadReservations();
});