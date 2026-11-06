// Mostra o endereço do CEP digitado (consulta a API pública ViaCEP).
// O endereço aparece só como dica: não é gravado, o banco guarda apenas o CEP.
(function () {
    const campo = document.getElementById('cep');
    const dica = document.getElementById('dica-cep');
    if (!campo || !dica) {
        return;
    }

    let consulta = 0;

    function mostrar(texto, erro) {
        dica.textContent = texto;
        dica.classList.toggle('erro', Boolean(erro));
    }

    async function buscar() {
        const cep = campo.value.replace(/\D/g, '');
        if (cep.length !== 8) {
            mostrar('');
            return;
        }
        const atual = ++consulta;
        mostrar('Buscando endereço...');
        try {
            const resposta = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
            const dados = await resposta.json();
            if (atual !== consulta) {
                return;
            }
            if (dados.erro) {
                mostrar('CEP não encontrado.', true);
                return;
            }
            const partes = [dados.logradouro, dados.bairro, dados.localidade + '/' + dados.uf];
            mostrar(partes.filter(Boolean).join(', '));
        } catch (e) {
            if (atual === consulta) {
                mostrar('Não foi possível consultar o endereço agora.', true);
            }
        }
    }

    campo.addEventListener('input', buscar);
    document.addEventListener('DOMContentLoaded', buscar);
})();
