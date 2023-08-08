/* Este listener lo que hace es tomar cada uno de los checkbox del arbol dom y les asigna un comportamiento el cual consiste en que si se selecciona uno,
se busca si algún otro checkbox fue seleccionado, si es así, le quita el check*/

document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tr'); // Obtener todas las filas
    rows.forEach(row => {
        const checkboxGroups = row.querySelectorAll('.checkbox-group');
        checkboxGroups.forEach(group => {
            group.addEventListener('change', function() {
                if (this.checked) {
                    checkboxGroups.forEach(otherGroup => {
                        if (otherGroup !== this && otherGroup.closest('tr') === row) {
                            otherGroup.checked = false;
                        }
                    });
                }
            });
        });
    });
});