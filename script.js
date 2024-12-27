const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
    container.classList.add('active');
})

loginBtn.addEventListener('click', () => {
    container.classList.remove('active');
})

  // Mostrar o pop-up quando a página for carregada
  window.onload = function() {
    setTimeout(function() {
        document.getElementById('popup').style.display = 'block';
        document.getElementById('popup-overlay').style.display = 'block';
        setTimeout(function() {
            document.getElementById('popup').style.opacity = 1;  // Fade-in
            document.getElementById('popup').style.transform = 'translate(-50%, -50%) scale(1)';  // Animação de crescimento
        }, 10); // Atraso para garantir que o display esteja alterado
    }, 500); // O pop-up aparece 500ms após o carregamento da página
};

// Fechar o pop-up quando clicar no botão "Fechar"
document.getElementById('close-btn').onclick = function() {
    document.getElementById('popup').style.opacity = 0;  // Fade-out
    document.getElementById('popup').style.transform = 'translate(-50%, -50%) scale(0.8)';  // Reduz o tamanho
    setTimeout(function() {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('popup-overlay').style.display = 'none';
    }, 400); // Espera a animação de fade-out terminar
};

// Fechar o pop-up ao clicar no fundo (overlay)
document.getElementById('popup-overlay').onclick = function() {
    document.getElementById('popup').style.opacity = 0;  // Fade-out
    document.getElementById('popup').style.transform = 'translate(-50%, -50%) scale(0.8)';  // Reduz o tamanho
    setTimeout(function() {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('popup-overlay').style.display = 'none';
    }, 400); // Espera a animação de fade-out terminar
};


 // Mostrar/Ocultar Senha
 const togglePassword = document.getElementById('toggle-password');
 const passwordField = document.getElementById('password');
 
 togglePassword.addEventListener('click', function() {
     // Alterna o tipo do campo de senha
     if (passwordField.type === 'password') {
         passwordField.type = 'text';
         togglePassword.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; // Muda o ícone para olho fechado
     } else {
         passwordField.type = 'password';
         togglePassword.innerHTML = '<i class="fa-solid fa-eye"></i>'; // Muda o ícone para olho aberto
     }
 });

 document.getElementById('signupForm').addEventListener('submit', function(event) {
     const name = document.getElementById('name').value.trim();
     const email = document.getElementById('email').value.trim();
     const password = document.getElementById('password').value.trim();
     
     const nameRegex = /^[A-Za-zÀ-ÿ\s]+$/;
     // Regex para validar e-mail (sem exigir ".com")
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
     
     // Verificar se o nome é válido
     

     // Verificar se o e-mail é válido e termina com ".com"
    // Verificar se o e-mail é válido
    if (!emailRegex.test(email)) {
        alert('Por favor, insira um e-mail válido.');
        event.preventDefault();
        return;
    }

     // Verificar o comprimento da senha
     if (password.length < 6) { // Mínimo de 6 caracteres, altere conforme necessário
         alert('A senha deve ter pelo menos 6 caracteres.');
         event.preventDefault();
         return;
     }
 });
