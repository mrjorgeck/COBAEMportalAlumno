// Livewire incluye Alpine.js automáticamente (se inyecta vía @livewireScripts).
// Aquí solo utilidades propias del portal.

/**
 * "Recordar CURP" (RF-33, SEG-11): solo localStorage, solo con consentimiento.
 * No guarda ningún otro dato. Ver docs/07 §3.
 */
window.curpStorage = {
    KEY: 'cobaem_curp',

    guardar(curp) {
        localStorage.setItem(this.KEY, curp.toUpperCase());
    },

    leer() {
        return localStorage.getItem(this.KEY) ?? '';
    },

    borrar() {
        localStorage.removeItem(this.KEY);
    },
};
