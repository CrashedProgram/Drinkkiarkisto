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

// Rekisteröintisivut (vaiheet)
const accountInfo = document.getElementById("account-info");
const accountCredentials = document.getElementById("account-credentials");
const accountAgreement = document.getElementById("account-agreement");

// Navigointinapit sivujen välillä
document.getElementById("account-info-next-button").addEventListener("click", switchPage(accountInfo, accountCredentials, 'forward'));
document.getElementById("account-credentials-back").addEventListener("click", switchPage(accountCredentials, accountInfo, 'backward'));
document.getElementById("account-credentials-next-button").addEventListener("click", switchPage(accountCredentials, accountAgreement, 'forward'));
document.getElementById("account-agreement-back").addEventListener("click", switchPage(accountAgreement, accountCredentials, 'backward'));

// Kirjautumislinkki
const registerLinkButton = document.getElementById("login-link-button");

registerLinkButton.addEventListener("click", async (e) => {
    e.preventDefault();
    console.log("Login link button clicked");
    await switchPage(accountInfo, null, 'forward')();
    await wait(300);
    window.location.href = "login.php";
});

// Sivun alustus
document.addEventListener("DOMContentLoaded", () => {
    console.log("DOMContentLoaded event fired");
    switchPage(null, accountInfo, 'forward')();
    updateNextButton();
    document.getElementById("account-credentials-next-button").classList.add("disabled");
});

// Validoi syötetty tieto (käyttäjänimi, email, salasana)
async function validateInput(type, input, iconElement) {
    console.log("Validating " + type);
    if (type === "username" || type === "email") {
        if (type === "username") {
            await wait(500);
        }
        try {
            // Lähetetään validointipyyntö palvelimelle
            const res = await fetch("includes/validate.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({type, value: input.value})
            });
            console.log(`DEBUG ${type} fetch response:`, res.status, res.statusText);
            const data = await res.json();
            console.log(`DEBUG ${type} validation payload:`, data);
            iconElement.classList.remove("loading-spinner-animation");
            
            // Näytetään oikea ikoni vastauksen perusteella
            if (data.valid) {
                iconElement.src = iconAssets.valid;
                iconElement.classList.add("valid-shake");
                setTimeout(() => {
                    iconElement.classList.remove("valid-shake");
                }, 1000);
            } else {
                iconElement.src = iconAssets.invalid;
                iconElement.classList.add("invalid-shake");
                setTimeout(() => {
                    iconElement.classList.remove("invalid-shake");
                }, 1000);
            }
            validity[type] = data.valid;
        } catch (error) {
            console.error(`DEBUG ${type} fetch error:`, error);
            iconElement.classList.remove("loading-spinner-animation");
            iconElement.src = iconAssets.error;
            validity[type] = false;
        }
        updateNextButton();
    } else if (type === "password") {
        iconElement.classList.remove("loading-spinner-animation");
        try {
            // Tarkistetaan salasanan vastaavuus
            const original = document.getElementById("password").value;
            const confirm = document.getElementById("password-confirm").value;

            const payload = {
                type: "password",
                value: confirm,
                compare: original
            };

            console.log("DEBUG password payload:", payload);
            const res = await fetch("includes/validate.php", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify(payload)
            });
            console.log("DEBUG password fetch response:", res.status, res.statusText);
            const data = await res.json();
            console.log("DEBUG password validation result:", data);

            // Päivitetään tila vastauksen mukaan
            iconElement.src = data.valid ? iconAssets.valid : iconAssets.invalid;
            iconElement.classList.add(data.valid ? "valid-shake" : "invalid-shake");
            setTimeout(() => {
                iconElement.classList.remove(data.valid ? "valid-shake" : "invalid-shake");
            }, 1000);

            validity.password = data.valid;
        } catch (error) {
            console.error("DEBUG password fetch error:", error);
            iconElement.src = iconAssets.error;
            validity.password = false;
        }
        updateCredentialsNextButton();
    }
}

// Ikonit eri tiloille
const iconAssets = {
    "valid": "assets/icons/input-check-22px.svg",
    "invalid": "assets/icons/input-attention-22px.svg",
    "loading": "assets/icons/input-loader-22px.svg",
    "error": "assets/icons/input-error-22px.svg"
};

// Haetaan lomakkeen elementit
const usernameInput = document.getElementById("username");
const usernameInputIcon = document.getElementById("username-input-icon");
const emailInput = document.getElementById("email");
const emailInputIcon = document.getElementById("email-input-icon");
const passwordInput = document.getElementById("password");
const passwordInputIcon = document.getElementById("password-input-icon");
const passwordConfirmInput = document.getElementById("password-confirm");
const passwordConfirmInputIcon = document.getElementById("password-confirm-input-icon");

// Seurataan kenttien kelpoisuutta
let validity = {username: false, email: false, password: false};

// Päivittää Seuraava-napin tilan käyttäjätietosivulla
function updateNextButton() {
    const btn = document.getElementById("account-info-next-button");
    if (validity.username && validity.email) {
        btn.classList.remove("disabled");
    } else {
        btn.classList.add("disabled");
    }
}

// Päivittää Seuraava-napin tilan salasanasivulla
function updateCredentialsNextButton() {
    const btn = document.getElementById("account-credentials-next-button");
    if (validity.password) {
        btn.classList.remove("disabled");
    } else {
        btn.classList.add("disabled");
    }
}

// Lisää validointikuuntelijan kenttiin
function addValidationListener(type, inputElem, iconElem, updateFn) {
    let typingTimeout;
    inputElem.addEventListener("input", () => {
        if (type === "password" && passwordConfirmInput.value === "") {
            return;
        }

        // Näytä latausikoni ja päivitä tila viiveellä
        clearTimeout(typingTimeout);
        iconElem.src = iconAssets.loading;
        iconElem.classList.add("loading-spinner-animation");
        iconElem.style.opacity = "1";
        validity[type] = false;
        updateFn();
        typingTimeout = setTimeout(async () => {
            await validateInput(type, inputElem, iconElem);
        }, 1000);
    });
}

// Liitetään validointi kaikkiin kenttiin
addValidationListener("username", usernameInput, usernameInputIcon, updateNextButton);
addValidationListener("email", emailInput, emailInputIcon, updateNextButton);
addValidationListener("password", passwordInput, passwordConfirmInputIcon, updateCredentialsNextButton);
addValidationListener("password", passwordConfirmInput, passwordConfirmInputIcon, updateCredentialsNextButton);
