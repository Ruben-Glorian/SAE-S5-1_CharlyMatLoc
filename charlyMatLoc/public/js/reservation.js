//Recup les réservations depuis l'api et affiche dynamiquement les cartes outils
function loadReservations() {
    const token = localStorage.getItem('access_token');
    if (!token) {
        window.location.href = 'signin.html';
        return;
    }

    fetch('/api/reservation', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
    .then(res => {
        const ct = (res.headers.get('content-type') || '').toLowerCase();
        if (!res.ok) {
            return res.text().then(text => { throw {status: res.status, body: text, contentType: ct}; });
        }
        if (!ct.includes('application/json')) {
            return res.text().then(text => { throw {status: res.status, body: text, contentType: ct}; });
        }
        return res.json();
    })
    .then(data => {
        const reservationsDiv = document.getElementById('reservations');
        const messageDiv = document.getElementById('reservation-message');
        if (!reservationsDiv) return;

        reservationsDiv.innerHTML = '';
        if (messageDiv) messageDiv.textContent = '';

        const items = data.reservations || [];
        if (!Array.isArray(items) || items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Aucune réservation trouvée.';
            reservationsDiv.appendChild(empty);
            return;
        }

        items.forEach(r => {
            const outil_id = r.outil_id;
            let card = reservationsDiv.querySelector(`.card[data-outil="${outil_id}"]`);

            if (card) {
                let ul = card.querySelector('.dates-list');
                if (!ul) {
                    ul = document.createElement('ul');
                    ul.className = 'dates-list';
                    card.appendChild(ul);
                }
                const li = document.createElement('li');
                li.textContent = r.date_location;
                ul.appendChild(li);

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

            //création d'une nouvelle carte
            card = document.createElement('div');
            card.className = 'card';
            card.dataset.outil = outil_id;

            const unitTarif = r.tarif;
            if (unitTarif > 0) card.dataset.tarif = String(unitTarif);

            const title = document.createElement('div');
            title.className = 'name';
            title.textContent = r.nom || r.outil_nom;
            card.appendChild(title);

            if (r.image_url) {
                const img = document.createElement('img');
                img.src = r.image_url;
                img.alt = r.nom || r.outil_nom || 'outil';
                card.appendChild(img);
            }

            const ul = document.createElement('ul');
            ul.className = 'dates-list';
            if (r.date_location) {
                const li = document.createElement('li');
                li.textContent = r.date_location;
                ul.appendChild(li);
            }
            card.appendChild(ul);

            if (r.date_reservation) {
                const dateRes = document.createElement('div');
                dateRes.className = 'meta';
                const d = new Date(r.date_reservation);
                const timeStr = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                dateRes.textContent = `Réservé le ${timeStr}`;
                card.appendChild(dateRes);
            }

            //tarif affiché si present
            if (unitTarif > 0) {
                const count = ul.children.length;
                const tarifDiv = document.createElement('div');
                tarifDiv.className = 'meta tarif';
                tarifDiv.textContent = `${unitTarif} € chacun` + (count > 0 ? ` — Sous‑total : ${unitTarif * count} €` : '');
                card.appendChild(tarifDiv);
            }


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
        console.error('Erreur chargement réservations', err);
        const reservationsDiv = document.getElementById('reservations');
        const messageDiv = document.getElementById('reservation-message');
        if (reservationsDiv) reservationsDiv.innerHTML = '';
        // Si err.body contient du HTML (commence par '<'), ne pas l'injecter dans le DOM, afficher message générique
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

//Initialiser au chargement de la page
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
