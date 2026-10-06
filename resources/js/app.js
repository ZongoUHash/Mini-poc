import QRCode from 'qrcode';
import { Html5Qrcode } from 'html5-qrcode';

document.querySelectorAll('.qr-code').forEach((canvas) => {
    QRCode.toCanvas(canvas, canvas.dataset.qrValue, { width: 220, margin: 1 });
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}

document.querySelectorAll('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const submitButton = form.querySelector('button[type="submit"], button:not([type])');

        if (!submitButton || submitButton.disabled) {
            return;
        }

        submitButton.disabled = true;
        submitButton.dataset.initialLabel = submitButton.textContent;
        submitButton.textContent = form.dataset.loadingLabel || 'Traitement en cours…';
    });
});

const installButton = document.querySelector('#install-app-button');
const isInstalled = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent)
    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
let deferredInstallPrompt;

if (installButton && !isInstalled) {
    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredInstallPrompt = event;
        installButton.classList.remove('hidden');
    });

    if (isIos) {
        installButton.classList.remove('hidden');
    }

    installButton.addEventListener('click', async () => {
        if (isIos) {
            window.alert('Pour installer l’application, utilisez Partager puis « Sur l’écran d’accueil » dans Safari.');
            return;
        }

        if (!deferredInstallPrompt) {
            window.alert('L’installation n’est pas encore disponible. Rechargez la page puis réessayez dans Chrome ou Edge.');
            return;
        }

        deferredInstallPrompt.prompt();
        await deferredInstallPrompt.userChoice;
        deferredInstallPrompt = undefined;
        installButton.classList.add('hidden');
    });

    window.addEventListener('appinstalled', () => installButton.classList.add('hidden'));
}

const pointButton = document.querySelector('#point-button');
const pointStatus = document.querySelector('#point-status');

if (pointButton && pointStatus) {
    pointButton.addEventListener('click', () => {
        if (!navigator.geolocation) {
            pointStatus.textContent = 'La géolocalisation n’est pas disponible sur cet appareil.';
            return;
        }

        const targetAccuracy = 10;
        const maximumSearchTime = 30000;
        let bestPosition = null;
        let isPointing = false;
        let locationWatchId;
        let precisionTimer;

        const setStatus = (message, color = 'text-slate-400') => {
            pointStatus.textContent = message;
            pointStatus.className = `mt-4 text-center text-sm ${color}`;
        };

        const submitPointing = async () => {
            if (!bestPosition || isPointing) {
                return;
            }

            isPointing = true;
            navigator.geolocation.clearWatch(locationWatchId);
            window.clearTimeout(precisionTimer);
            const accuracy = Math.round(bestPosition.coords.accuracy);
            setStatus(`Position obtenue avec une précision de ± ${accuracy} m. Enregistrement du pointage…`, 'text-blue-200');

            try {
                const response = await fetch(pointButton.dataset.pointUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ latitude: bestPosition.coords.latitude, longitude: bestPosition.coords.longitude, accuracy_meters: bestPosition.coords.accuracy }),
                });
                const body = await response.json();
                setStatus(body.message, response.ok ? 'text-emerald-300' : 'text-red-300');
                if (!response.ok) pointButton.disabled = false;
            } catch {
                setStatus('Connexion impossible. Réessayez.', 'text-red-300');
                pointButton.disabled = false;
            }
        };

        pointButton.disabled = true;
        setStatus('Recherche d’une position précise à 10 m ou moins. Restez près d’une fenêtre ou à l’extérieur…', 'text-blue-200');

        locationWatchId = navigator.geolocation.watchPosition((position) => {
            if (!bestPosition || position.coords.accuracy < bestPosition.coords.accuracy) {
                bestPosition = position;
            }

            const accuracy = Math.round(bestPosition.coords.accuracy);
            if (accuracy <= targetAccuracy) {
                submitPointing();
                return;
            }

            setStatus(`Précision actuelle : ± ${accuracy} m. Le pointage sera validé dès que la précision atteint 10 m ou moins…`, 'text-blue-200');
        }, (error) => {
            window.clearTimeout(precisionTimer);
            navigator.geolocation.clearWatch(locationWatchId);
            setStatus(error.code === 1 ? 'La position doit être autorisée pour pointer.' : 'Position indisponible. Vérifiez le GPS puis réessayez.', 'text-red-300');
            pointButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: maximumSearchTime, maximumAge: 0 });

        precisionTimer = window.setTimeout(() => {
            if (bestPosition) {
                const accuracy = Math.round(bestPosition.coords.accuracy);
                navigator.geolocation.clearWatch(locationWatchId);
                setStatus(`Précision insuffisante : ± ${accuracy} m. Le pointage n’a pas été enregistré. Activez le GPS haute précision, rapprochez-vous d’une fenêtre ou allez à l’extérieur, puis réessayez.`, 'text-red-300');
                pointButton.disabled = false;
                return;
            }

            navigator.geolocation.clearWatch(locationWatchId);
            setStatus('La position n’a pas pu être déterminée. Vérifiez le GPS puis réessayez.', 'text-red-300');
            pointButton.disabled = false;
        }, maximumSearchTime);
    });
}

const scanButton = document.querySelector('#scan-button');
const scanStatus = document.querySelector('#scan-status');

if (scanButton && scanStatus) {
    scanButton.addEventListener('click', async () => {
        scanButton.disabled = true;
        scanStatus.textContent = 'Ouverture de la caméra…';
        const scanner = new Html5Qrcode('qr-reader');

        try {
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                async (decodedText) => {
                    await scanner.stop();
                    const scanUrl = new URL(decodedText, window.location.origin);
                    if (!scanUrl.pathname.startsWith('/pointer/')) {
                        scanStatus.textContent = 'Ce QR ne correspond pas au POC de pointage.';
                        scanButton.disabled = false;
                        return;
                    }
                    window.location.assign(scanUrl.toString());
                },
                () => {},
            );
            scanStatus.textContent = 'Cadrez le QR code affiché par le responsable RH.';
        } catch {
            scanStatus.textContent = 'Impossible d’ouvrir la caméra. Vérifiez son autorisation et utilisez une adresse HTTPS sur mobile.';
            scanButton.disabled = false;
        }
    });
}

const attendanceBoard = document.querySelector('#attendance-board');

if (attendanceBoard) {
    const refreshBoard = () => window.location.reload();
    const updateCountdowns = () => {
        let nextExpiration = null;

        document.querySelectorAll('[data-board-session]').forEach((session) => {
            const expiresAt = new Date(session.dataset.expiresAt).getTime();
            const secondsRemaining = Math.ceil((expiresAt - Date.now()) / 1000);
            const countdown = session.querySelector('[data-countdown]');

            if (secondsRemaining <= 0) {
                session.remove();
                refreshBoard();
                return;
            }

            if (countdown) {
                const minutes = Math.floor(secondsRemaining / 60);
                const seconds = String(secondsRemaining % 60).padStart(2, '0');
                countdown.textContent = `${minutes}:${seconds}`;
            }

            nextExpiration = nextExpiration === null ? secondsRemaining : Math.min(nextExpiration, secondsRemaining);
        });

        if (nextExpiration !== null) {
            window.setTimeout(refreshBoard, (nextExpiration * 1000) + 250);
        }
    };

    updateCountdowns();
    window.setInterval(updateCountdowns, 1000);

    window.setInterval(async () => {
        try {
            const response = await fetch(attendanceBoard.dataset.boardStatusUrl, { headers: { Accept: 'application/json' } });
            const board = await response.json();

            if (board.signature !== attendanceBoard.dataset.boardSignature) {
                refreshBoard();
            }
        } catch {
            // The display remains usable with its current QR if the connection is briefly unavailable.
        }
    }, 5000);
}
