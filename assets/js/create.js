// Ainesosien lisäys lomakkeeseen
const addBtn = document.querySelector('.add-ingredient-button');
const ingredientsSection = addBtn.closest('.inputs');
const template = ingredientsSection.querySelector('.ingredient-wrapper');

// Lisää poistotoiminnallisuuden ainesosariville
function bindDeleteIcon(wrapper) {
    const del = wrapper.querySelector('.ingredient-delete-icon');
    del.addEventListener('click', () => wrapper.remove());
}

// Aktivoi poistotoiminto malliriville
bindDeleteIcon(template);

// Ainesosan lisäyspainikkeen toiminto
addBtn.addEventListener('click', () => {
    const clone = template.cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value = '');
    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    bindDeleteIcon(clone);
    ingredientsSection.insertBefore(clone, addBtn);
});

// Lomakkeen käsittely
const form = document.querySelector('form');
const errorContainer = document.getElementById('create-error');
const successContainer = document.getElementById('create-success');

form.addEventListener('submit', async e => {
    e.preventDefault();
    errorContainer.style.display = 'none';
    successContainer.style.display = 'none';
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    // Kerätään lomakkeen tiedot JSON-objektiin
    const fd = new FormData(form);
    const payload = {};
    fd.forEach((v, k) => {
        if (k.endsWith('[]')) {
            const name = k.slice(0, -2);
            payload[name] = payload[name] || [];
            payload[name].push(v);
        } else {
            payload[k] = v;
        }
    });

    try {
        // Lähetetään lomake palvelimelle
        const res = await fetch('includes/create_logic.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const json = await res.json();
        
        // Käsitellään vastaus
        if (json.success) {
            successContainer.textContent = json.message || 'Onnistui!';
            successContainer.style.display = 'block';
            form.style.display = 'none';
        } else {
            if (json.error === 'name_taken') {
                errorContainer.textContent = 'Juoman nimi on jo käytössä, valitse eri nimi.';
            } else {
                errorContainer.textContent = 'Tapahtui virhe. Yritä myöhemmin uudelleen.';
            }
            errorContainer.style.display = 'block';
            submitBtn.disabled = false;
        }
    } catch (err) {
        console.error('Submission error:', err);
        errorContainer.textContent = 'Verkkovirhe, yritä uudelleen.';
        errorContainer.style.display = 'block';
        submitBtn.disabled = false;
    }
});
