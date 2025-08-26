document.addEventListener('DOMContentLoaded', async () => {
    const rowContainer = document.getElementById('drinks-table');
    const template = document.getElementById('drink-row-template');

    // Tyhjentää taulukon rivit
    function clearRows() {
        rowContainer.querySelectorAll('.table-row').forEach(row => row.remove());
    }

    // Hakee juomat palvelimelta
    async function fetchDrinks() {
        try {
            const res = await fetch('includes/manage_logic.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'getDrinks'})
            });
            
            if (!res.ok) return [];
            return await res.json();
        } catch (err) {
            console.error('Error fetching drinks:', err);
            return [];
        }
    }

    // Renderöi juomat taulukkoon
    function renderDrinks(drinks) {
        clearRows();
        drinks.forEach(drink => {
            const clone = template.content.cloneNode(true);
            clone.querySelector('.drink-id').textContent = drink.id;
            clone.querySelector('.drink-name').textContent = drink.name;
            const catEl = clone.querySelector('.drink-category');
            catEl.textContent = drink.category || '';
            catEl.dataset.hasAllergens = drink.has_allergens;
            clone.querySelector('.drink-adder-id').textContent = drink.added_by || '';
            rowContainer.appendChild(clone);
        });
    }

    // Liittää toiminnot riveihin (muokkaus ja poisto)
    function bindRowActions() {
        const overlay = document.querySelector('.overlay');
        const editPopup = document.getElementById('edit-drink-popup');
        const deletePopup = document.getElementById('delete-drink-popup');
        const editForm = document.getElementById('edit-drink-form');
        const deleteConfirmBtn = document.querySelector('.delete-confirm-btn');
        const deleteCancelBtn = document.querySelector('.delete-cancel-btn');
        let selectedId = null;

        // Liittää toiminnot riveihin
        rowContainer.querySelectorAll('.table-row').forEach(row => {
            const id = row.querySelector('.drink-id').textContent;
            const nm = row.querySelector('.drink-name').textContent;
            const has = row.querySelector('.drink-category').dataset.hasAllergens === '1';

            // Muokkaus-painike
            row.querySelector('.edit-btn').addEventListener('click', () => {
                selectedId = id;
                document.getElementById('edit-drink-id').value = id;
                document.getElementById('edit-drink-name').value = nm;
                document.getElementById('edit-has-allergens').checked = has;
                overlay.classList.add('active');
                editPopup.classList.add('active');
            });

            // Poisto-painike
            row.querySelector('.delete-btn').addEventListener('click', () => {
                selectedId = id;
                overlay.classList.add('active');
                deletePopup.classList.add('active');
            });
        });

        // Juoman päivitys lomakkeesta
        editForm.addEventListener('submit', async e => {
            e.preventDefault();
            try {
                const name = document.getElementById('edit-drink-name').value;
                const hasAllergens = document.getElementById('edit-has-allergens').checked;
                
                const res = await fetch('includes/manage_logic.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'updateDrink', id: selectedId, name, hasAllergens})
                });
                
                if (!res.ok) {
                    throw new Error('Failed to update drink');
                }
                
                closeAll();
                const drinks = await fetchDrinks();
                renderDrinks(drinks);
                bindRowActions();
            } catch (err) {
                console.error('Error updating drink:', err);
                alert('Virhe päivittäessä juomaa. Yritä uudelleen.');
            }
        });

        // Juoman poisto vahvistuksen jälkeen
        deleteConfirmBtn.addEventListener('click', async () => {
            try {
                const res = await fetch('includes/manage_logic.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'deleteDrink', id: selectedId})
                });
                
                if (!res.ok) {
                    throw new Error('Failed to delete drink');
                }
                
                closeAll();
                const drinks = await fetchDrinks();
                renderDrinks(drinks);
                bindRowActions();
            } catch (err) {
                console.error('Error deleting drink:', err);
                alert('Virhe poistaessa juomaa. Yritä uudelleen.');
            }
        });
        
        // Peruuta-painike
        deleteCancelBtn.addEventListener('click', closeAll);

        // Sulkee popup-ikkunat
        function closeAll() {
            overlay.classList.remove('active');
            editPopup.classList.remove('active');
            deletePopup.classList.remove('active');
        }
    }

    // Lataa juomat sivun alussa
    try {
        const drinks = await fetchDrinks();
        renderDrinks(drinks);
        bindRowActions();
    } catch (err) {
        console.error('Initial load error:', err);
    }
});
