console.log("Register script loaded");

// Odotustoiminto animaatioita varten
async function wait(time) {
    return new Promise(resolve => setTimeout(resolve, time));    
}

// Sivujen vaihtamiseen käytettävä funktio, animoi siirtymät
function switchPage(hideElement, showElement, direction = 'forward') {
    return async () => {
        if (hideElement) {
            if (direction === 'forward') {
                hideElement.style.transform = "translateX(-20px)";
            } else {
                hideElement.style.transform = "translateX(20px)";
            }
            hideElement.style.opacity = "0";
        }
        await wait(300);
        if (hideElement) {
            hideElement.style.display = "none";
            hideElement.style.transform = "translateX(0)";
        }
        if (showElement) {
            showElement.style.display = "flex";
            if (direction === 'forward') {
                showElement.style.transform = "translateX(20px)";
            } else {
                showElement.style.transform = "translateX(-20px)";
            }
            await wait(10);
            showElement.style.opacity = "1";
            showElement.style.transform = "translateX(0)";
        }
    };
}

// Ikonit kenttien tiloille
const iconAssets = {
    "valid": "assets/icons/input-check-22px.svg",
    "invalid": "assets/icons/input-attention-22px.svg",
    "loading": "assets/icons/input-loader-22px.svg",
    "error": "assets/icons/input-error-22px.svg"
};

const passwordInputIcon = document.getElementById("password-input-icon");

// Animoi kentän virheellisen syötteen
function showInvalidCredentials() {
    // Näytetään virheikoni vain salasanakentälle
    passwordInputIcon.src = iconAssets.invalid;
    passwordInputIcon.style.opacity = "1"; // Varmistetaan että ikoni on näkyvissä
    
    // Lisätään tärinäanimaatio salasanakentälle
    passwordInputIcon.classList.add("invalid-shake");
    
    // Poistetaan tärinäanimaatio kun se on valmis
    setTimeout(() => {
        passwordInputIcon.classList.remove("invalid-shake");
    }, 1000);
}

// Päivittää kirjautumispainikkeen tilan kenttien sisällön perusteella
function updateLoginButton() {
    const btn = document.getElementById("submit-button");
    const user = document.getElementById("username").value.trim();
    const pass = document.getElementById("password").value.trim();

    // Aktivoidaan painike vain kun molemmat kentät ovat täytetty
    if (user && pass) {
        btn.classList.remove("disabled");
    } else {
        btn.classList.add("disabled");
    }
}

const loginContainer = document.getElementById("login-container");

// Rekisteröitymislinkki
const registerLinkButton = document.getElementById("register-link-button");

registerLinkButton.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("Register link button clicked");
    await switchPage(loginContainer, null, 'forward')();
    await wait(300);
    window.location.href = "register.php";
});

// Sivun alustus
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOMContentLoaded event fired");
    switchPage(null, loginContainer, 'forward')();

    updateLoginButton();
    document.getElementById("username").addEventListener("input", updateLoginButton);
    document.getElementById("password").addEventListener("input", updateLoginButton);
    
    // Varmistetaan että ikonit ovat piilotettuna sivun latautuessa
    const usernameInputIcon = document.getElementById("username-input-icon");
    if (usernameInputIcon) usernameInputIcon.style.opacity = "0";
    if (passwordInputIcon) passwordInputIcon.style.opacity = "0";
});

// Estetään normaali lomakkeen lähetys, käytetään AJAX-kirjautumista
const loginForm = document.querySelector('form');
loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const user = document.getElementById("username").value.trim();
    const pass = document.getElementById("password").value.trim();
    const csrf = document.querySelector('input[name="csrf_token"]').value;
    
    // Näytetään latausikoni vain salasanakentällä
    passwordInputIcon.src = iconAssets.loading;
    passwordInputIcon.style.opacity = "1";
    
    try {
        const res = await fetch("includes/login_logic.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ username: user, password: pass, csrf_token: csrf })
        });
        
        const data = await res.json();
        
        if (data.success) {
            // Kirjautuminen onnistui, ohjataan etusivulle
            window.location.href = "index.php";
            return;
        }
        
        // Näytetään virheelliset tunnukset jos kirjautuminen epäonnistui
        showInvalidCredentials();
    } catch (error) {
        console.error("Kirjautumisvirhe:", error);
        passwordInputIcon.src = iconAssets.error;
        passwordInputIcon.style.opacity = "1"; // Varmistetaan että ikoni on näkyvissä
    }
});

