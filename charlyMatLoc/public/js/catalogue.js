let allOutils = [];
let currentPage = 1;
const itemsPerPage = 10;

//charger les outils depuis l'api
async function chargerCatalogue() {
    try {
        const res = await fetch('/api/outils');
        if (!res.ok) throw new Error("Erreur serveur");
        allOutils = await res.json();
        afficherCatalogue();
    } catch (err) {
        console.error(err);
        document.getElementById('catalogue').innerHTML = "<p>Erreur de chargement du catalogue.</p>";
    }
}

//filtres dynamiques
function filtrerOutils() {
    const name = document.getElementById('searchName')?.value.toLowerCase() || '';
    const cat = document.getElementById('filterCategory')?.value || '';
    const minPrice = parseFloat(document.getElementById('minPrice')?.value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice')?.value) || Infinity;

    return allOutils.filter(o => {
        const matchName = o.nom.toLowerCase().includes(name);
        const matchCat = cat === "" || o.categorie_id == cat;
        const matchPrix = o.tarif >= minPrice && o.tarif <= maxPrice;
        return matchName && matchCat && matchPrix;
    });
}

//affichage du catalogue avec pagination
function afficherCatalogue() {
    const catalogueDiv = document.getElementById('catalogue');
    const outilsFiltres = filtrerOutils();

    const totalPages = Math.ceil(outilsFiltres.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageOutils = outilsFiltres.slice(start, end);

    catalogueDiv.innerHTML = '';

    if (pageOutils.length === 0) {
        catalogueDiv.innerHTML = '<p>Aucun outil trouvé.</p>';
        afficherPagination(totalPages);
        return;
    }

    pageOutils.forEach(outil => {
        const card = document.createElement('div');
        card.className = 'card';

        //Image
        const img = document.createElement('img');
        img.src = outil.image_url || "../images/default-tool.jpg";
        img.alt = outil.nom;
        img.onclick = () => window.location.href = `detailsOutil.html?id=${outil.id}`;
        card.appendChild(img);

        //nom
        const name = document.createElement('div');
        name.className = 'name';
        name.textContent = outil.nom;
        card.appendChild(name);

        //desc
        const desc = document.createElement('p');
        desc.textContent = outil.description || "";
        card.appendChild(desc);

        //prix + exemplaires
        const meta = document.createElement('div');
        meta.className = 'meta';
        if (typeof outil.nb_exemplaires !== 'undefined') {
            meta.textContent =
                `${outil.tarif} € / jour — ${outil.nb_exemplaires} ${outil.nb_exemplaires === 1 ? 'exemplaire' : 'exemplaires'} disponibles`;
        } else {
            meta.textContent = `${outil.tarif} € / jour — quantité inconnue`;
        }
        card.appendChild(meta);

        //formulaire d’ajout au panier
        const form = document.createElement('form');
        form.className = 'add-panier-form';

        const labelDebut = document.createElement('label');
        labelDebut.textContent = 'Du : ';
        form.appendChild(labelDebut);

        const dateDebutInput = document.createElement('input');
        dateDebutInput.type = 'date';
        dateDebutInput.required = true;
        dateDebutInput.min = new Date().toISOString().split('T')[0];
        form.appendChild(dateDebutInput);

        const labelFin = document.createElement('label');
        labelFin.textContent = ' au ';
        form.appendChild(labelFin);

        const dateFinInput = document.createElement('input');
        dateFinInput.type = 'date';
        dateFinInput.required = true;
        dateFinInput.min = new Date().toISOString().split('T')[0];
        form.appendChild(dateFinInput);

        const btn = document.createElement('button');
        btn.type = 'submit';
        btn.textContent = 'Ajouter au panier';
        form.appendChild(btn);

        form.onsubmit = async (e) => {
            e.preventDefault();
            const dateDebut = dateDebutInput.value;
            const dateFin = dateFinInput.value;

            if (!dateDebut || !dateFin) {
                alert('Veuillez choisir une période');
                return;
            }
            if (dateFin < dateDebut) {
                alert('La date de fin doit être après la date de début');
                return;
            }

            const token = localStorage.getItem('access_token');
            if (!token) {
                window.location.href = 'signin.html';
                return;
            }

            try {
                const res = await fetch('/api/panier', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        outil_id: outil.id,
                        date_debut: dateDebut,
                        date_fin: dateFin
                    })
                });

                const result = await res.json();

                if (res.ok && result.message) {
                    alert('Outil ajouté au panier !');
                } else {
                    alert(result.error || 'Erreur lors de l\'ajout au panier');
                }
            } catch (error) {
                alert('Erreur réseau');
            }
        };

        card.appendChild(form);
        catalogueDiv.appendChild(card);
    });

    afficherPagination(totalPages);
}

//pagination
function afficherPagination(totalPages) {
    const paginationDiv = document.getElementById('pagination');
    if (!paginationDiv) return;

    paginationDiv.innerHTML = '';

    if (totalPages <= 1) return;

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === currentPage) btn.disabled = true;
        btn.onclick = () => {
            currentPage = i;
            afficherCatalogue();
        };
        paginationDiv.appendChild(btn);
    }
}

//application des filtres
document.getElementById('applyFilters')?.addEventListener('click', () => {
    currentPage = 1;
    afficherCatalogue();
});

//chargement des catégories dans le filtre (optionnel)
async function chargerCategories() {
    const select = document.getElementById('filterCategory');
    if (!select) return;

    try {
        const res = await fetch('/api/categories');
        if (!res.ok) throw new Error('Erreur chargement catégories');
        const categories = await res.json();

        select.innerHTML = '<option value="">Toutes les catégories</option>';
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.nom;
            select.appendChild(option);
        });
    } catch {
        console.warn('Impossible de charger les catégories');
    }
}

//chargement initial
chargerCategories();
chargerCatalogue();