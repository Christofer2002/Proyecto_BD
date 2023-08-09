document.addEventListener('DOMContentLoaded', () => {
    uncheck();
});



// CREO QUE HAY QUE CAMBIAR LA ESTRUCTURA
const evaluateButton = document.getElementById('evaluateButton');

evaluateButton.addEventListener('click', () => {
    changeColorSemaphore(evaluate());
});



/**
 * The function uncheck() is used to uncheck all checkboxes in a group when one checkbox in the group
 * is checked.
 */
function uncheck(){
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
}





// ESTO NO ESTÁ TERMINADO, PERO LA IDEA AQUI ES TOMAR LOS AFIRMATIVOS Y HACER LA DIVISION ENTRE TODOS LOS CHECKBOX
function evaluate (){
    const AffirmativechecklistItems = document.querySelectorAll('');
    const totalCheckboxes = document.querySelectorAll('tr');
    return ( AffirmativechecklistItems/ totalCheckboxes) * 100;
}




/**
 * The function changes the color of a semaphore based on a given percentage.
 * @param percentage - The `percentage` parameter represents the percentage value that determines the
 * color of the semaphore lights.
 */

function changeColorSemaphore(percentage){
    const luzRoja = document.querySelector('.luz luz-roja');
    const luzAmarilla = document.querySelector('.luz luz-amarilla');
    const luzVerde = document.querySelector('.luz luz-verde');

    if (percentage < 50) {
        luzRoja.style.backgroundColor = 'red';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'gray';
    } else if (percentage >= 50 && percentage <= 80) {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'yellow';
        luzVerde.style.backgroundColor = 'gray';
    } else {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'green';
    }
}


