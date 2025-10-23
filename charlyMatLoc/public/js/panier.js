//Recup le panier depuis l'api et affiche dynamiquement les cartes outils
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
        //vide le conteneur avant d'ajouter les nouveaux elems
        panierDiv.innerHTML = '';
        // Utilise le tableau d'outils retourné par l'API
        const outils = data.panier || [];
        if (outils.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Votre panier est vide.';
            panierDiv.appendChild(empty);
            totalDiv.textContent = '';
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
    });
}

// Charger le panier au chargement de la page
loadPanier();
