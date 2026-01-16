// Importar usuarios predefinidos
const usersModule = document.createElement('script');
usersModule.src = '../../js/login.js';
document.head.appendChild(usersModule);

// Esperar a que se cargue el módulo de usuarios
usersModule.onload = () => {
    loadUsers();
};

// Función para cargar usuarios en la tabla
function loadUsers() {
    const tableBody = document.getElementById('userTableBody');
    tableBody.innerHTML = '';

    users.forEach(user => {
        const tr = document.createElement('tr');
        // Obtener permisos guardados
        const savedPermissions = JSON.parse(localStorage.getItem(`permissions_${user.username}`) || '[]');
        const permissionsList = savedPermissions.length > 0 ? savedPermissions.join(', ') : 'Permisos por defecto según rol';

        tr.innerHTML = `
            <td>${user.username}</td>
            <td>${user.role}</td>
            <td><span class="badge bg-success">Activo</span></td>
            <td><small>${permissionsList}</small></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editUser('${user.username}')"><i class="bi bi-pencil"></i></button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// Función para editar usuario
function editUser(username) {
    const user = users.find(u => u.username === username);
    if (!user) return;

    document.getElementById('userId').value = username;
    document.getElementById('username').value = username;
    document.getElementById('password').value = '';
    document.getElementById('role').value = user.role;

    // Cargar permisos guardados o por defecto según rol
    const savedPermissions = JSON.parse(localStorage.getItem(`permissions_${username}`) || '[]');
    if (savedPermissions.length > 0) {
        loadSavedPermissions(savedPermissions);
    } else {
        loadPermissionsByRole(user.role);
    }

    document.getElementById('modalTitle').textContent = 'Editar Usuario';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

// Función para cargar permisos guardados
function loadSavedPermissions(permissions) {
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.checked = permissions.includes(checkbox.value);
    });
}

// Función para cargar permisos según el rol
function loadPermissionsByRole(role) {
    // Limpiar todos los checkboxes
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Asignar permisos según el rol
    switch(role) {
        case 'admin':
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.checked = true;
            });
            break;
        case 'directiva':
            document.getElementById('permiso_directiva').checked = true;
            document.getElementById('permiso_reportes').checked = true;
            break;
        case 'dh':
            document.getElementById('permiso_reportes').checked = true;
            break;
    }
}

// Evento para cambio de rol
document.getElementById('role').addEventListener('change', function() {
    const userId = document.getElementById('userId').value;
    if (!userId) { // Solo cargar permisos por rol si es usuario nuevo
        loadPermissionsByRole(this.value);
    }
});

// Función para guardar cambios del usuario
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    
    // Obtener permisos seleccionados
    const permissions = Array.from(document.querySelectorAll('.form-check-input:checked'))
        .map(checkbox => checkbox.value);

    // Guardar permisos en localStorage
    localStorage.setItem(`permissions_${username}`, JSON.stringify(permissions));

    // Cerrar modal y recargar tabla
    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
    loadUsers();
    
    alert('Permisos actualizados correctamente');
});

// Validación de acceso a páginas
function validatePageAccess(username, page) {
    const user = users.find(u => u.username === username);
    if (!user) return false;

    // Obtener permisos guardados o usar permisos por defecto según rol
    const savedPermissions = JSON.parse(localStorage.getItem(`permissions_${username}`) || '[]');
    if (savedPermissions.length > 0) {
        return savedPermissions.includes(page);
    }

    // Validar según rol por defecto
    switch(user.role) {
        case 'admin':
            return true;
        case 'directiva':
            return page === 'directiva_general.html' || page === 'Reportes_Directores.html';
        case 'dh':
            return page === 'Reportes_Directores.html';
        default:
            return false;
    }
}