fetch('/catalogue/api')
    .then(res => res.json())
    .then(data => {
        const catalogueDiv = document.getElementById('catalogue');
        catalogueDiv.innerHTML = '';
        if (data.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty';
            empty.textContent = 'Aucun outil disponible.';
            catalogueDiv.appendChild(empty);
            return;
        }
        data.forEach(outil => {
            const card = document.createElement('div');
            card.className = 'card';

            const img = document.createElement('img');
            img.src = outil.image_url;
            img.alt = outil.nom;
            card.appendChild(img);

            const name = document.createElement('div');
            name.className = 'name';
            name.textContent = outil.nom;
            card.appendChild(name);

            const meta = document.createElement('div');
            meta.className = 'meta';
            meta.textContent = '1 exemplaire disponible'; // adapte si besoin
            card.appendChild(meta);

            catalogueDiv.appendChild(card);
        });
    });