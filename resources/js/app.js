import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function applyCnpjMask(input) {
    input.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');

        if (value.length > 14) value = value.slice(0, 14);

        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');

        this.value = value;
    });
}

function attachCnpjMaskWhenAvailable() {
    const tryAttach = () => {
        const input = document.getElementById('cnpj');
        if (input && !input.dataset.cnpjMaskApplied) {
            applyCnpjMask(input);
            input.dataset.cnpjMaskApplied = '1';
        }
    };

    // tenta já
    tryAttach();

    // observa mudanças no DOM e tenta de novo quando a página “montar”
    const observer = new MutationObserver(() => {
        tryAttach();
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });
}

attachCnpjMaskWhenAvailable();

