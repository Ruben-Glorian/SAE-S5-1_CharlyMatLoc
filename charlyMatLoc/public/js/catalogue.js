let allOutils = [];
let currentPage = 1;
const itemsPerPage = 6;

async function chargerCatalogue() {
    const res = await fetch('/api/outils');
    allOutils = await res.json();
    afficherCatalogue();
}

function filtrerOutils() {
    const name = document.getElementById('searchName').value.toLowerCase();
    const cat = document.getElementById('filterCategory').value;
    const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;

    return allOutils.filter(o => {
        const matchName = o.nom.toLowerCase().includes(name);
        const matchCat = cat === "" || o.categorie_id == cat;
        const matchPrix = o.tarif >= minPrice && o.tarif <= maxPrice;
        return matchName && matchCat && matchPrix;
    });
}

function afficherCatalogue() {
    const catalogueDiv = document.getElementById('catalogue');
    const outilsFiltres = filtrerOutils();

    // Pagination
    const totalPages = Math.ceil(outilsFiltres.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageOutils = outilsFiltres.slice(start, end);

    catalogueDiv.innerHTML = '';

    if (pageOutils.length === 0) {
        catalogueDiv.innerHTML = '<p>Aucun outil trouvé.</p>';
        return;
    }

    pageOutils.forEach(outil => {
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <img src="${outil.image_url}" alt="${outil.nom}">
            <h3>${outil.nom}</h3>
            <p>${outil.tarif} € / jour</p>
            <p>${outil.nb_exemplaires} exemplaires disponibles</p>
        `;
        catalogueDiv.appendChild(card);
    });

    afficherPagination(totalPages);
}

function afficherPagination(totalPages) {
    const paginationDiv = document.getElementById('pagination');
    paginationDiv.innerHTML = '';

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

document.getElementById('applyFilters').addEventListener('click', () => {
    currentPage = 1;
    afficherCatalogue();
});

chargerCatalogue();
