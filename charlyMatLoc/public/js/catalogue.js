//recup le catalogue d'outils depuis l'api et affiche dynamiquement les cartes outils
fetch('/api/outils')
    .then(res => res.json())
    .then(data => {
        const catalogueDiv = document.getElementById('catalogue');
        catalogueDiv.innerHTML = '';
        //si aucun outil dispo
        if (data.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Aucun outil disponible.';
            catalogueDiv.appendChild(empty);
            return;
        }
        //carte des outils
        data.forEach(outil => {
            const card = document.createElement('div');
            card.className = 'card';

            //img
            const img = document.createElement('img');
            img.src = outil.image_url;
            img.alt = outil.nom;
            card.appendChild(img);
            //click pour le détail
            img.onclick = () => {
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
            if (typeof outil.stock_affiche !== 'undefined') {
                if (outil.stock_affiche > 0) {
                    meta.textContent = outil.stock_affiche + (outil.stock_affiche === 1 ? ' exemplaire disponible' : ' exemplaires disponibles');
                } else {
                    meta.textContent = 'Rupture de stock';
                }
            } else if (typeof outil.nb_exemplaires !== 'undefined') {
                if (outil.nb_exemplaires > 0) {
                    meta.textContent = outil.nb_exemplaires + (outil.nb_exemplaires === 1 ? ' exemplaire disponible' : ' exemplaires disponibles');
                } else {
                    meta.textContent = 'Rupture de stock';
                }
            }
            card.appendChild(meta);

            //formulaire d'ajout au panier
            const form = document.createElement('form');
            form.className = 'add-panier-form';
            form.onsubmit = function(e) {
                e.preventDefault(); //empêche le rechargement de la page
                const dateDebut = dateDebutInput.value;
                const dateFin = dateFinInput.value;
                //verif des champs date
                if (!dateDebut || !dateFin) {
                    alert('Veuillez choisir une période');
                    return;
                }
                if (dateFin < dateDebut) {
                    alert('La date de fin doit être après la date de début');
                    return;
                }
                //verif la présence du token d'authentification
                const token = localStorage.getItem('access_token');
                if (!token) {
                    window.location.href = 'signin.html';
                    return;
                }
                //envoie la requête d'ajout au panier à l'api
                fetch('/api/panier', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ outil_id: outil.id, date_debut: dateDebut, date_fin: dateFin })
                })
                    .then(res => res.json())
                    .then(result => {
                        // Affiche un message selon le résultat
                        if (result.message) {
                            alert('Outil ajouté au panier !');
                        } else {
                            alert(result.error || 'Erreur lors de l\'ajout au panier');
                        }
                    })
                    .catch(() => alert('Erreur réseau'));
            };

            //date début
            const labelDebut = document.createElement('label');
            labelDebut.htmlFor = 'date-debut-' + outil.id;
            labelDebut.textContent = 'Du : ';
            form.appendChild(labelDebut);

            const dateDebutInput = document.createElement('input');
            dateDebutInput.type = 'date';
            dateDebutInput.id = 'date-debut-' + outil.id;
            dateDebutInput.name = 'date_debut';
            dateDebutInput.required = true;
            dateDebutInput.min = new Date().toISOString().split('T')[0]; // Date min = aujourd'hui
            form.appendChild(dateDebutInput);

            //date fin
            const labelFin = document.createElement('label');
            labelFin.htmlFor = 'date-fin-' + outil.id;
            labelFin.textContent = ' au : ';
            form.appendChild(labelFin);

            const dateFinInput = document.createElement('input');
            dateFinInput.type = 'date';
            dateFinInput.id = 'date-fin-' + outil.id;
            dateFinInput.name = 'date_fin';
            dateFinInput.required = true;
            dateFinInput.min = new Date().toISOString().split('T')[0]; //date min = ajd
            form.appendChild(dateFinInput);

            //ajout panier
            const btn = document.createElement('button');
            btn.type = 'submit';
            btn.textContent = 'Ajouter au panier';
            form.appendChild(btn);

            card.appendChild(form);
            catalogueDiv.appendChild(card);
        });
    })