// Häivyttää hakutulokset ennen uusien näyttämistä
async function fadeOutResults() {
    const container = document.querySelector('.search-results');
    container.querySelectorAll('.result-item').forEach(item => {
        item.classList.add('fade-out');
    });

    await new Promise(r => setTimeout(r, 300));
    container.innerHTML = '';
}

// Hakutoiminnallisuus: hakee ja näyttää tulokset
async function search(query, filterBtn, icon) {
    try {
        await fadeOutResults();

        // Näytetään latausikoni
        icon.src = iconAssets.loading;
        icon.classList.add('loading-spinner-animation');

        const res = await fetch("includes/search_logic.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                query: query.trim(),
                filter: filterBtn.id.split("-")[2]
            })
        });

        icon.classList.remove('loading-spinner-animation');

        if (!res.ok) {
            const errText = await res.text();
            console.error("Non-JSON response:", errText);
            icon.src = iconAssets.error;
            return;
        }

        // Käsitellään ja näytetään hakutulokset
        const data = await res.json();
        icon.src = iconAssets.search;
        resultsContainer.innerHTML = '';
        
        data.forEach((item, index) => {
            const clone = resultTemplate.content.cloneNode(true);
            const el = clone.querySelector('.result-item');
            el.classList.add('fade-in');
            el.style.animationDelay = `${index * fadeInDelay}ms`;
            clone.querySelector('.result-item-name').textContent = item.name;
            
            // Muodostetaan infoteksti
            const count = item.ingredient_count || 0;
            const suffix = count === 1 ? ' ainesosa' : ' ainesosaa';
            clone.querySelector('.result-item-info').textContent =
                `${item.category} • ${count}${suffix}`;
            const likes = item.likes_count || 0;
            const dislikes = item.dislikes_count || 0;
            const percent = likes + dislikes
                ? `${Math.round((likes / (likes + dislikes)) * 100)}%`
                : '0%';
            clone.querySelector('.result-item-like-count').textContent = percent;
            
            // Varoitusteksti allergeeneista tai puuttuvista ohjeista
            const warning = item.has_allergens
                ? 'Sisältää allergeenejä'
                : (item.recipe_notes ? '' : 'Ei ohjeta');
            clone.querySelector('.result-item-warning').textContent = warning;
            
            // Linkki reseptiin
            const link = clone.querySelector('.result-item-button-link');
            link.href = `recipe.php?id=${item.id}`;
            link.textContent = 'Näytä';
            resultsContainer.appendChild(clone);
        });
    } catch (e) {
        console.error(e);
        icon.classList.remove('loading-spinner-animation');
        icon.src = iconAssets.error;
    }
}

// Ikonit eri tiloille
const iconAssets = {
    search: "assets/icons/input-search-22px.svg",
    loading: "assets/icons/input-loader-22px.svg",
    error: "assets/icons/input-error-22px.svg"
};
const fadeInDelay = 50; // Efektin viive millisekunteina

// Haetaan DOM-elementit
const searchInput = document.getElementById('search-input');
const searchIcon = document.getElementById('search-indicator');
const filterButtons = {
    all: document.getElementById('search-filter-all'),
    /* drinks: document.getElementById('search-filter-drinks'),
    description: document.getElementById('search-filter-description'),
    categories: document.getElementById('search-filter-categories'), */
    name: document.getElementById('search-filter-name'),
    ingredient: document.getElementById('search-filter-ingredient'),
};
const resultTemplate = document.getElementById('result-item-template');
const resultsContainer = document.querySelector('.search-results');
const searchClear = document.getElementById('search-clear');

// Tyhjennä-napin toiminnallisuus
searchClear.addEventListener('click', () => {
    searchInput.value = '';
    searchInput.focus();
    search(searchInput.value, activeFilter, searchIcon);
});

// Asetetaan oletusfilteri
let activeFilter = filterButtons.all;
activeFilter.classList.add('active');

// Filtereiden käsittely
Object.keys(filterButtons).forEach(name => {
    filterButtons[name].addEventListener('click', () => {
        console.log("Filter clicked:", name);
        activeFilter.classList.remove('active');
        activeFilter = filterButtons[name];
        activeFilter.classList.add('active');

        clearTimeout(typingTimeout);
        searchIcon.src = iconAssets.loading;
        searchIcon.classList.add("loading-spinner-animation");
        search(searchInput.value, activeFilter, searchIcon);
    });
});

// Hakukentän käsittely viiveellä (debounce)
let typingTimeout;

searchInput.addEventListener('input', () => {
    console.log("input event:", searchInput.value);
    clearTimeout(typingTimeout);

    searchIcon.src = iconAssets.loading;
    searchIcon.classList.add("loading-spinner-animation");

    typingTimeout = setTimeout(() => {
        console.log("debounced search for:", searchInput.value);
        search(searchInput.value, activeFilter, searchIcon);
    }, 500);
});

// Suoritetaan alustava haku sivun latautuessa
(async () => {
    try {
        await search("", activeFilter, searchIcon);
    } catch (err) {
        console.error("Initial search failed:", err);
    }
})();
