document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');
    const phoneInput = document.getElementById('phone-number');
    
    // =========================================================
    // 1. MÁSCARA DE TELEFONE (Limitação e Formatação)
    // =========================================================

    function applyPhoneMask(value) {
        // Remove tudo que não for dígito
        value = value.replace(/\D/g, ''); 
        
        // Limita a 11 dígitos (DDD + 9 dígitos)
        value = value.substring(0, 11); 

        // Aplica a máscara: (99) 99999-9999
        if (value.length > 0) {
            value = '(' + value;
            if (value.length > 3) {
                value = value.substring(0, 3) + ') ' + value.substring(3);
            }
            if (value.length > 10) {
                value = value.substring(0, 10) + '-' + value.substring(10);
            }
        }
        return value;
    }

    phoneInput.addEventListener('input', (event) => {
        event.target.value = applyPhoneMask(event.target.value);
    });

    // =========================================================
    // 2. TOGGLE DE VISIBILIDADE DA SENHA (Ícone de Olho)
    // =========================================================
    
    document.querySelectorAll('.password-toggle').forEach(icon => {
        icon.addEventListener('click', (event) => {
            const targetId = event.target.dataset.target;
            const targetInput = document.getElementById(targetId);
            const isPassword = targetInput.type === 'password';

            // Alterna o tipo do input e o ícone
            targetInput.type = isPassword ? 'text' : 'password';
            event.target.name = isPassword ? 'eye-off-outline' : 'eye-outline';
        });
    });

    // =========================================================
    // 3. VALIDAÇÕES NO SUBMIT
    // =========================================================

    form.addEventListener('submit', function(event) {
        let isValid = true;
        
        // Array para armazenar o primeiro campo inválido
        let firstInvalidField = null;

        // Limpa mensagens de erro anteriores
        document.querySelectorAll('.form-group').forEach(group => group.classList.remove('has-error'));
        document.querySelectorAll('.validation-message').forEach(msg => msg.textContent = '');
        
        const fields = [
            { id: 'fullName', name: 'Nome Completo', required: true },
            { id: 'dateOfBirth', name: 'Data de Nascimento', required: true },
            { id: 'gender', name: 'Gênero', required: true, isSelect: true },
            { id: 'phone-number', name: 'Telefone', required: false, isPhone: true },
            { id: 'email', name: 'E-mail', required: true, isEmail: true },
            { id: 'password', name: 'Senha', required: true, isPassword: true },
            { id: 'confirmPassword', name: 'Confirmar Senha', required: true, isConfirm: true }
        ];

        // Itera sobre todos os campos e aplica as regras
        fields.forEach(field => {
            const input = document.getElementById(field.id);
            const value = input.value.trim();
            const messageElement = document.getElementById(`${field.id}-message`);

            // A. VERIFICAÇÃO DE CAMPO VAZIO
            if (field.required && (value === '' || (field.isSelect && input.value === ''))) {
                messageElement.textContent = `O campo ${field.name} é obrigatório.`;
                input.parentElement.closest('.form-group').classList.add('has-error');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = input;
                return;
            }

            // B. VALIDAÇÃO DE E-MAIL (Simulação de Realidade)
            if (field.isEmail) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailRegex.test(value)) {
                    messageElement.textContent = 'Por favor, insira um e-mail válido (ex: nome@dominio.com).';
                    input.parentElement.closest('.form-group').classList.add('has-error');
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = input;
                    return;
                }
            }

            // C. VALIDAÇÃO DE TELEFONE (Com Limite de Dígitos)
            if (field.isPhone) {
                const cleanNumber = value.replace(/\D/g, '');
                // 10 dígitos (DDD + número) ou 11 (DDD + 9º dígito)
                if (cleanNumber.length > 0 && (cleanNumber.length < 10 || cleanNumber.length > 11)) {
                    messageElement.textContent = 'O telefone deve ter 10 ou 11 dígitos (DDD incluso).';
                    input.parentElement.closest('.form-group').classList.add('has-error');
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = input;
                    return;
                }
            }

            // D. VALIDAÇÃO DE SENHA (6 Caracteres, Apenas Letras/Números)
            if (field.isPassword) {
                const passwordRegex = /^[a-zA-Z0-9]{1,6}$/;
                if (!passwordRegex.test(value)) {
                    messageElement.textContent = 'A senha deve ter 1 a 6 caracteres (apenas letras e números).';
                    input.parentElement.closest('.form-group').classList.add('has-error');
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = input;
                    return;
                }
            }
        });
        
        // E. VALIDAÇÃO DE CONFIRMAÇÃO DE SENHA
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirmPassword');
        if (passwordInput && confirmInput && passwordInput.value !== confirmInput.value) {
            document.getElementById('confirmPassword-message').textContent = 'As senhas não coincidem!';
            confirmInput.parentElement.closest('.form-group').classList.add('has-error');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = confirmInput;
        }

        // VERIFICAÇÃO FINAL: Se for inválido, previne o envio e foca no erro
        if (!isValid) {
            event.preventDefault();
            if (firstInvalidField) {
                 // Rola a página até o primeiro campo com erro
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' }); 
                firstInvalidField.focus();
            }
        }
        // Se isValid for true, o formulário é enviado para o PHP
    });
});