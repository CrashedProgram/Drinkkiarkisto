document.addEventListener('DOMContentLoaded', async () => {
    const table = document.getElementById('users-table');
    const tpl = document.getElementById('user-row-template');

    // Tyhjentää taulukon rivit
    function clearRows() {
        table.querySelectorAll('.table-row').forEach(r => r.remove());
    }

    // Hakee käyttäjät palvelimelta
    async function fetchUsers() {
        try {
            const res = await fetch('includes/manage_logic.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({action: 'getUsers'})
            });
            
            if (!res.ok) {
                throw new Error('Failed to fetch users');
            }
            
            return await res.json();
        } catch (err) {
            console.error('Error fetching users:', err);
            return [];
        }
    }

    // Renderöi käyttäjät taulukkoon
    function renderUsers(users) {
        clearRows();
        users.forEach(u => {
            const clone = tpl.content.cloneNode(true);
            clone.querySelector('.user-id').textContent = u.id;
            clone.querySelector('.user-name').textContent = u.username;
            clone.querySelector('.user-email').textContent = u.email;
            table.appendChild(clone);
        });
    }

    // Liittää toiminnot riveihin (poisto)
    function bindActions() {
        const overlay = document.querySelector('.overlay');
        const popup = document.getElementById('delete-user-popup');
        const btnDel = popup.querySelector('.delete-confirm-btn');
        const btnCancel = popup.querySelector('.delete-cancel-btn');
        let selId = null;

        // Liittää poistotoiminnon riveihin
        table.querySelectorAll('.table-row').forEach(row => {
            const id = row.querySelector('.user-id').textContent;
            row.querySelector('.delete-btn').addEventListener('click', () => {
                selId = id;
                overlay.classList.add('active');
                popup.classList.add('active');
            });
        });

        // Käyttäjän poisto vahvistuksen jälkeen
        btnDel.addEventListener('click', async () => {
            try {
                const res = await fetch('includes/manage_logic.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({action: 'deleteUser', id: selId})
                });
                
                if (!res.ok) {
                    throw new Error('Failed to delete user');
                }
                
                closeAll();
                const users = await fetchUsers();
                renderUsers(users);
                bindActions();
            } catch (err) {
                console.error('Error deleting user:', err);
                alert('Virhe poistaessa käyttäjää. Yritä uudelleen.');
            }
        });
        
        // Peruuta-painike
        btnCancel.addEventListener('click', closeAll);

        // Sulkee popup-ikkunan
        function closeAll() {
            overlay.classList.remove('active');
            popup.classList.remove('active');
        }
    }

    // Lataa käyttäjät sivun alussa
    try {
        const users = await fetchUsers();
        renderUsers(users);
        bindActions();
    } catch (err) {
        console.error('Initial load error:', err);
    }
});
