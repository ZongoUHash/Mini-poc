import QRCode from 'qrcode';
import { Html5Qrcode } from 'html5-qrcode';

document.querySelectorAll('.qr-code').forEach((canvas) => {
    QRCode.toCanvas(canvas, canvas.dataset.qrValue, { width: 220, margin: 1 });
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}

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

        pointButton.disabled = true;
        pointStatus.textContent = 'Localisation en cours…';
        navigator.geolocation.getCurrentPosition(async (position) => {
            try {
                const response = await fetch(pointButton.dataset.pointUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ latitude: position.coords.latitude, longitude: position.coords.longitude, accuracy_meters: position.coords.accuracy }),
                });
                const body = await response.json();
                pointStatus.textContent = body.message;
                pointStatus.className = `mt-4 text-center text-sm ${response.ok ? 'text-emerald-300' : 'text-red-300'}`;
                if (!response.ok) pointButton.disabled = false;
            } catch {
                pointStatus.textContent = 'Connexion impossible. Réessayez.';
                pointStatus.className = 'mt-4 text-center text-sm text-red-300';
                pointButton.disabled = false;
            }
        }, (error) => {
            pointStatus.textContent = error.code === 1 ? 'La position doit être autorisée pour pointer.' : 'Position indisponible. Réessayez.';
            pointStatus.className = 'mt-4 text-center text-sm text-red-300';
            pointButton.disabled = false;
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
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
