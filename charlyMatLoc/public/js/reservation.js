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
            const card = document.createElement('div');
            card.className = 'card';

            const title = document.createElement('div');
            title.className = 'name';
            title.textContent = r.nom || r.outil_nom || (`Réservation #${r.id || ''}`);
            card.appendChild(title);

            if (r.image_url) {
                const img = document.createElement('img');
                img.src = r.image_url;
                img.alt = r.nom || 'outil';
                card.appendChild(img);
            }

            if (r.date_location) {
                const dateLoc = document.createElement('div');
                dateLoc.className = 'meta';
                dateLoc.textContent = `Location le ${r.date_location}`;
                card.appendChild(dateLoc);
            }

            if (r.date_reservation) {
                const dateRes = document.createElement('div');
                dateRes.className = 'meta';
                dateRes.textContent = `Réservé le ${r.date_reservation}`;
                card.appendChild(dateRes);
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
