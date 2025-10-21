//recup le catalogue d'outils depuis l'api et affiche dynamiquement les cartes outils
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
            //ajout d'un clic sur une image pour voir le detail de l'outil
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
            meta.textContent = '1 exemplaire disponible';
            card.appendChild(meta);

            //formulaire d'ajout au panier
            const form = document.createElement('form');
            form.className = 'add-panier-form';
            form.onsubmit = function(e) {
                e.preventDefault();
                const date = dateInput.value;
                if (!date) {
                    alert('Veuillez choisir une date');
                    return;
                }
                fetch('/api/panier/ajouter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ outil_id: outil.id, date: date })
                })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        alert('Outil ajouté au panier !');
                    } else {
                        alert(result.message || 'Erreur lors de l\'ajout au panier');
                    }
                })
                .catch(() => alert('Erreur réseau'));
            };

            //champ pour la date
            const label = document.createElement('label');
            label.htmlFor = 'date-' + outil.id;
            label.textContent = 'Date : ';
            form.appendChild(label);

            const dateInput = document.createElement('input');
            dateInput.type = 'date';
            dateInput.id = 'date-' + outil.id;
            dateInput.name = 'date';
            dateInput.required = true;
            //min = aujourd'hui
            dateInput.min = new Date().toISOString().split('T')[0];
            form.appendChild(dateInput);

            //bouton d'ajout au panier
            const btn = document.createElement('button');
            btn.type = 'submit';
            btn.textContent = 'Ajouter au panier';
            form.appendChild(btn);

            card.appendChild(form);
            catalogueDiv.appendChild(card);
        });
    });