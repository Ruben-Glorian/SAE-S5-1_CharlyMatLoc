function afficherDetailsOutil() {
    const detailsDiv = document.getElementById('details');
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (!id) {
        detailsDiv.innerHTML = '<p>Aucun détail trouvé pour cet outil.</p>';
        return;
    }
    fetch(`/api/outils/${id}`)
        .then(res => res.ok ? res.json() : null)
        .then(outil => {
            if (!outil) {
                detailsDiv.innerHTML = '<p>Aucun détail trouvé pour cet outil.</p>';
                return;
            }
            detailsDiv.innerHTML = `
                <h2>${outil.nom}</h2>
                <img class = 'image' src="${outil.image_url}" alt="${outil.nom}" style="max-width:300px;max-height:200px;display:block;margin-bottom:16px;">
                <p><strong>Description :</strong> ${outil.description || 'Non renseignée'}</p>
                <p><strong>Tarif :</strong> ${outil.tarif ? outil.tarif + ' €' : 'Non renseigné'}</p>
                <p><strong>Catégorie :</strong> ${outil.categorie || 'Non renseignée'}</p>
                <button onclick="window.history.back()">Retour au catalogue</button>
            `;
        });
}

window.onload = afficherDetailsOutil;
