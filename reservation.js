fetch('horaires.json')
  .then(response => response.json())
  .then(data => {
    const select = document.getElementById("filtre");
    const horairesDiv = document.getElementById("horaires");

    // Ajouter les groupes au menu déroulant
    data.sous_groupe.forEach((groupe, index) => {
      const option = document.createElement("option");
      option.value = index;
      option.textContent = `${groupe.nom} - ${groupe.annee} (${groupe.filiaire})`;
      select.appendChild(option);
    });

    // Affichage des horaires
    select.addEventListener("change", (e) => {
      const index = e.target.value;
      const groupe = data.sous_groupe[index];
      if (!groupe) return;

      const matin = groupe.horaires[0].matin[0];
      const soir = groupe.horaires[0].soir[0];

      horairesDiv.innerHTML = `
        <h2>${groupe.nom} - ${groupe.annee} (${groupe.filiaire})</h2>
        <h3>Matin</h3>
        <ul>${Object.entries(matin).map(([jour, heure]) => `<li>${jour} : ${heure}</li>`).join('')}</ul>
        <h3>Soir</h3>
        <ul>${Object.entries(soir).map(([jour, heure]) => `<li>${jour} : ${heure}</li>`).join('')}</ul>
      `;
    });
  })
  .catch(error => console.error('Erreur lors du chargement du JSON :', error));
