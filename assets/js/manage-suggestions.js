document.addEventListener('DOMContentLoaded', async () => {
    const table = document.getElementById('suggestions-table');
    const tpl = document.getElementById('suggestion-row-template');

    // Tyhjentää taulukon rivit
    function clear() {
        table.querySelectorAll('.table-row').forEach(r => r.remove());
    }

    // Hakee ehdotukset palvelimelta
    async function fetchSuggestions() {
        try {
            const res = await fetch('includes/manage_logic.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'getDrinkSuggestions'})
            });
            
            if (!res.ok) {
                throw new Error('Failed to fetch suggestions');
            }
            
            return await res.json();
        } catch (err) {
            console.error('Error fetching suggestions:', err);
            return [];
        }
    }

    // Renderöi ehdotukset taulukkoon
    function render(sugs) {
        clear();
        sugs.forEach(s => {
            const c = tpl.content.cloneNode(true);
            c.querySelector('.suggestion-id').textContent = s.id;
            c.querySelector('.suggestion-name').textContent = s.name;
            c.querySelector('.suggestion-by').textContent = s.suggested_by;
            c.querySelector('.suggestion-date').textContent = s.created_at;
            table.appendChild(c);
        });
    }

    // Liittää toiminnot riveihin (hyväksyminen ja poisto)
    function bind() {
        table.querySelectorAll('.table-row').forEach(row => {
            const id = row.querySelector('.suggestion-id').textContent;
            
            // Ehdotuksen hyväksyminen
            row.querySelector('.approve-btn').addEventListener('click', async () => {
                try {
                    const res = await fetch('includes/manage_logic.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({action: 'approveSuggestion', id})
                    });
                    
                    if (!res.ok) {
                        throw new Error('Failed to approve suggestion');
                    }
                    
                    await reload();
                } catch (err) {
                    console.error('Error approving suggestion:', err);
                    alert('Virhe hyväksyessä ehdotusta. Yritä uudelleen.');
                }
            });
            
            // Ehdotuksen poistaminen
            row.querySelector('.delete-btn').addEventListener('click', async () => {
                try {
                    const res = await fetch('includes/manage_logic.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({action: 'deleteSuggestion', id})
                    });
                    
                    if (!res.ok) {
                        throw new Error('Failed to delete suggestion');
                    }
                    
                    await reload();
                } catch (err) {
                    console.error('Error deleting suggestion:', err);
                    alert('Virhe poistaessa ehdotusta. Yritä uudelleen.');
                }
            });
        });
    }

    // Päivittää ehdotukset
    async function reload() {
        try {
            const sugs = await fetchSuggestions();
            render(sugs);
            bind();
        } catch (err) {
            console.error('Error reloading suggestions:', err);
        }
    }

    // Lataa ehdotukset sivun alussa
    try {
        await reload();
    } catch (err) {
        console.error('Initial load error:', err);
    }
});
