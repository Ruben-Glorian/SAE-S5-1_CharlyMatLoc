//Recup le catalogue d'outils depuis l'api et affiche dynamiquement les cartes outils
fetch('/catalogue/api')
    .then(res => res.json())
    .then(data => {
        //selectionne le conteneur du catalogue
        const catalogueDiv = document.getElementById('catalogue');
        //vide le conteneur avant d'ajouter les nouveaux elems
        catalogueDiv.innerHTML = '';
        if (data.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Aucun outil disponible.';
            catalogueDiv.appendChild(empty);
            return;
        }
        //crée une carte et l'ajoute au catalogue pour chaque outil
        data.forEach(outil => {
            const card = document.createElement('div');
            card.className = 'card';

            //img de l'outil
            const img = document.createElement('img');
            img.src = outil.image_url;
            img.alt = outil.nom;
            card.appendChild(img);
            card.onclick = () => {
                window.location.href = `detailsOutil.html?id=${outil.id}`;
            }

            //nom
            const name = document.createElement('div');
            name.className = 'name';
            name.textContent = outil.nom;
            card.appendChild(name);

            //nb exemplaire
            const meta = document.createElement('div');
            meta.className = 'meta';
            meta.textContent = '1 exemplaire disponible'; // À adapter si besoin
            card.appendChild(meta);

            //ajoute la carte au catalogue
            catalogueDiv.appendChild(card);
        });
    });