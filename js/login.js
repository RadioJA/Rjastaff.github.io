// Usuarios predefinidos
const users = [

    // Usuarios administradores
    { username: 'Natanael', password: 'admin', role: 'admin' },
    { username: 'Juan', password: 'jcmdl', role: 'directiva' },
    { username: 'Anne', password: 'aasg', role: 'directiva' },
    { username: 'Rosario', password: 'rbddh', role: 'directiva' },
    { username: 'karina', password: 'kcsg', role: 'directiva' },

    //Usuarios Directores Lunes a Viernes
    { username: 'Xiomara', password: 'xlv5-7am', role: 'dh' },
    { username: 'Karina', password: 'klv7-9am', role: 'dh' },
    { username: 'Valeria', password: 'lv9-11am', role: 'dh' },
    { username: 'Maria', password: 'mlv11-1pm', role: 'dh' },
    { username: 'Aleida', password: 'alv1-3pm', role: 'dh' },
    { username: 'Luis', password: 'llv3-5pm', role: 'dh' },
    { username: 'Glenda', password: 'glv5-7pm', role: 'dh' },
    { username: 'Paty', password: 'plv7-9pm', role: 'dh' },
    { username: 'Dumas', password: 'dlv9-11pm', role: 'dh' },

    // Usuarios Directores sabado
    { username: 'Karina', password: 'ks7-9am', role: 'dh' },
    { username: 'Mary', password: 'ms9-1pm', role: 'dh' },

    // Usuarios Directores Domingo
    { username: 'Xiomara', password: 'xd7-9am', role: 'dh' },
    { username: 'Marilu', password: 'md9-11am', role: 'dh' },
    { username: 'Luis', password: 'ld11-1pm', role: 'dh' },
    

    // Usuarios Directores de sabado y domingo
    { username: 'Kamelfi', password: 'ksd1-3pm', role: 'dh' },
    { username: 'Alexander', password: 'asd3-5pm', role: 'dh' },
    { username: 'Lety', password: 'lsd5-7pm', role: 'dh' },
    { username: 'Ruben', password: 'rsd7-9pm', role: 'dh' },
    { username: 'Naty', password: 'nrmlv9-11pm', role: 'dh' }

];

document.getElementById('loginForm').addEventListener('submit', handleLogin);

function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');

    // Buscar usuario
    const user = users.find(u => u.username === username && u.password === password);

    if (user) {
        // Guardar información del usuario en sessionStorage
        sessionStorage.setItem('user', JSON.stringify({
            username: user.username,
            role: user.role,
            token: btoa(user.username + ':' + Date.now()) // Token simple para demostración
        }));

        // Redirigir según el rol
        switch(user.role) {
            case 'admin':
                window.location.href = 'panel/panel_admin.html';
                break;
            case 'dh':
                window.location.href = 'panel/Reportes_Directores.html';
                break;
            case 'directiva':
                window.location.href = 'panel/directiva_general.html';
                break;
            default:
                showError('Tipo de usuario no válido');
        }
    } else {
        showError('Usuario o contraseña incorrectos');
    }
}

function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    setTimeout(() => {
        errorMessage.style.display = 'none';
    }, 3000);
}