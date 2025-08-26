document.addEventListener('DOMContentLoaded', () => {
    // Haetaan DOM-elementit
    const likeBtn = document.querySelector('.like-button');
    const dislikeBtn = document.querySelector('.dislike-button');
    const rating = document.querySelector('.rating');

    // Äänestysfunktio juoman arviointiin
    async function sendVote(btn, isLike) {
        const id = btn.dataset.drinkId;

        // Estetään painikkeet äänestyksen ajaksi
        if (likeBtn) likeBtn.disabled = true;
        if (dislikeBtn) dislikeBtn.disabled = true;

        try {
            // Lähetetään ääni palvelimelle - päivitetty polku
            const res = await fetch('includes/vote_logic.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ drink_id: +id, is_like: isLike })
            });

            if (!res.ok) {
                throw new Error(`Server returned ${res.status}`);
            }

            const data = await res.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Päivitetään näkyvä arvio
            rating.textContent = `${data.likes_count}/${data.dislikes_count}`;

            // Päivitetään painikkeiden tilat
            if (likeBtn) {
                likeBtn.disabled = isLike;
                likeBtn.setAttribute('aria-pressed', isLike ? 'true' : 'false');
            }

            if (dislikeBtn) {
                dislikeBtn.disabled = !isLike;
                dislikeBtn.setAttribute('aria-pressed', !isLike ? 'true' : 'false');
            }
        } catch (err) {
            console.error('Vote error:', err);

            // Palautetaan painikkeet käyttöön virheen sattuessa
            if (likeBtn) likeBtn.disabled = false;
            if (dislikeBtn) dislikeBtn.disabled = false;

            alert('Äänen tallentaminen epäonnistui. Yritä uudelleen.');
        }
    }

    // Liitetään tapahtumankäsittelijät painikkeisiin
    if (likeBtn) {
        likeBtn.addEventListener('click', e => sendVote(e.currentTarget, true));
    }

    if (dislikeBtn) {
        dislikeBtn.addEventListener('click', e => sendVote(e.currentTarget, false));
    }
});
