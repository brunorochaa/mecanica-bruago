// Máscaras de digitação. Uso: data-mascara="telefone" | "cep" | "placa" no campo.
// O servidor continua validando e normalizando tudo; a máscara é só ajuda visual.
(function () {
    const apenasDigitos = (texto) => texto.replace(/\D/g, '');

    const mascaras = {
        telefone(valor) {
            const d = apenasDigitos(valor).slice(0, 11);
            if (d.length <= 2) {
                return d.length ? '(' + d : '';
            }
            const fim = d.length > 10 ? 7 : 6;
            const resto = d.slice(2, fim) + (d.length > 6 ? '-' + d.slice(fim) : '');
            return '(' + d.slice(0, 2) + ') ' + resto;
        },
        cep(valor) {
            const d = apenasDigitos(valor).slice(0, 8);
            return d.length > 5 ? d.slice(0, 5) + '-' + d.slice(5) : d;
        },
        placa(valor) {
            return valor.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7);
        },
    };

    document.addEventListener('input', (evento) => {
        const campo = evento.target;
        const mascara = mascaras[campo.dataset?.mascara];
        if (mascara) {
            campo.value = mascara(campo.value);
        }
    });

    // Aplica também nos valores que já vêm preenchidos (edição).
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-mascara]').forEach((campo) => {
            const mascara = mascaras[campo.dataset.mascara];
            if (mascara) {
                campo.value = mascara(campo.value);
            }
        });
    });
})();
