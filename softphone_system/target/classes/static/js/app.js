// Variables globales
let callStartTime = null;
let callTimerInterval = null;
let isConnected = false;
let isInCall = false;
const API_BASE = 'http://localhost:8080/api/softphone';

// Éléments DOM
const connectBtn = document.getElementById('connectBtn');
const disconnectBtn = document.getElementById('disconnectBtn');
const callBtn = document.getElementById('callBtn');
const hangupBtn = document.getElementById('hangupBtn');
const extensionInput = document.getElementById('extension');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const phoneNumberInput = document.getElementById('phoneNumber');
const contextSelect = document.getElementById('context');
const statusBadge = document.getElementById('statusBadge');
const userInfo = document.getElementById('userInfo');
const callDurationDisplay = document.getElementById('callDuration');
const callStatusDisplay = document.getElementById('callStatus');
const historyList = document.getElementById('historyList');
const backspaceBtn = document.getElementById('backspaceBtn');

// Listeners pour les boutons du clavier numérique
document.querySelectorAll('.dialpad-btn[data-digit]').forEach(btn => {
    btn.addEventListener('click', () => {
        const digit = btn.getAttribute('data-digit');
        phoneNumberInput.value += digit;
    });
});

// Bouton retour
backspaceBtn.addEventListener('click', () => {
    const current = phoneNumberInput.value;
    phoneNumberInput.value = current.slice(0, -1);
});

// Connexion
connectBtn.addEventListener('click', async () => {
    const extension = extensionInput.value.trim();
    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (!extension || !username || !password) {
        alert('Veuillez remplir tous les champs');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/connect`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ extension, username, password })
        });

        const data = await response.json();
        if (data.success) {
            isConnected = true;
            updateUI();
            showNotification(`Connecté en tant que ${extension}`, 'success');
        }
    } catch (error) {
        console.error('Erreur de connexion:', error);
        showNotification('Erreur de connexion', 'error');
    }
});

// Déconnexion
disconnectBtn.addEventListener('click', async () => {
    try {
        await fetch(`${API_BASE}/disconnect`, { method: 'POST' });
        isConnected = false;
        isInCall = false;
        if (callTimerInterval) clearInterval(callTimerInterval);
        updateUI();
        showNotification('Déconnecté', 'info');
    } catch (error) {
        console.error('Erreur de déconnexion:', error);
    }
});

// Appel
callBtn.addEventListener('click', async () => {
    const phoneNumber = phoneNumberInput.value.trim();
    const context = contextSelect.value;

    if (!phoneNumber) {
        alert('Entrez un numéro');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/call`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phoneNumber, context })
        });

        const data = await response.json();
        if (data.success) {
            isInCall = true;
            callStartTime = Date.now();
            startCallTimer();
            updateUI();
            showNotification(`Appel vers ${phoneNumber}`, 'success');
        }
    } catch (error) {
        console.error('Erreur lors de l\'appel:', error);
        showNotification('Erreur lors de l\'appel', 'error');
    }
});

// Raccrocher
hangupBtn.addEventListener('click', async () => {
    try {
        await fetch(`${API_BASE}/hangup`, { method: 'POST' });
        isInCall = false;
        if (callTimerInterval) clearInterval(callTimerInterval);
        updateUI();
        loadCallHistory();
        showNotification('Appel terminé', 'info');
    } catch (error) {
        console.error('Erreur lors du raccrochage:', error);
    }
});

// Mettre à jour l'interface
function updateUI() {
    // Statut
    statusBadge.textContent = isConnected ? 'Connecté' : 'Déconnecté';
    statusBadge.classList.toggle('connected', isConnected);

    // Infos utilisateur
    if (isConnected) {
        userInfo.textContent = `${extensionInput.value}`;
    } else {
        userInfo.textContent = '';
    }

    // Boutons
    connectBtn.disabled = isConnected;
    disconnectBtn.disabled = !isConnected;
    extensionInput.disabled = isConnected;
    usernameInput.disabled = isConnected;
    passwordInput.disabled = isConnected;
    phoneNumberInput.disabled = !isConnected;
    contextSelect.disabled = !isConnected;
    callBtn.disabled = !isConnected || isInCall;
    hangupBtn.disabled = !isInCall;

    // Affichage de l'état d'appel
    if (isInCall) {
        callStatusDisplay.textContent = 'En appel';
        callStatusDisplay.style.color = 'var(--success)';
    } else {
        callStatusDisplay.textContent = 'Prêt';
        callStatusDisplay.style.color = 'var(--success)';
        callDurationDisplay.textContent = '0:00';
    }
}

// Démarrer le chrono d'appel
function startCallTimer() {
    if (callTimerInterval) clearInterval(callTimerInterval);
    
    callTimerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - callStartTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        callDurationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

// Charger l'historique
async function loadCallHistory() {
    try {
        const response = await fetch(`${API_BASE}/call-history`);
        const calls = await response.json();

        historyList.innerHTML = '';
        if (calls.length === 0) {
            historyList.innerHTML = '<p class="empty-message">Aucun appel enregistré</p>';
            return;
        }

        calls.forEach(call => {
            const duration = call.duration || 'N/A';
            const timestamp = new Date(call.timestamp).toLocaleTimeString('fr-FR');
            
            const item = document.createElement('div');
            item.className = 'history-item';
            item.innerHTML = `
                <div>
                    <div class="history-item-phone">${call.phoneNumber}</div>
                    <div class="history-item-type">${call.status}</div>
                </div>
                <div class="history-item-time">${timestamp}</div>
            `;
            historyList.appendChild(item);
        });
    } catch (error) {
        console.error('Erreur lors du chargement de l\'historique:', error);
    }
}

// Notification
function showNotification(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    // Vous pouvez ajouter un système de notification visuel ici
}

// Initialisation
updateUI();
loadCallHistory();

// Recharger l'historique toutes les 5 secondes
setInterval(loadCallHistory, 5000);
