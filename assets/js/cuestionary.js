document.addEventListener('DOMContentLoaded', () => {
    uncheck();
});



// CREO QUE HAY QUE CAMBIAR LA ESTRUCTURA

var porcentaje;


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
    var questions;
    var affirmativeQuestion = 0;
    var naQuestion = 0;

    const rows = document.querySelectorAll('tr'); // Obtener todas las filas
    questions = rows.length - 1;
    rows.forEach(row => {
        const checkboxGroups = row.querySelectorAll('.checkbox-group');
        checkboxGroups.forEach(cheackB => {
            if (cheackB.checked){
                if(cheackB.value === "YES" ){
                    affirmativeQuestion = affirmativeQuestion + 1;
                }else if(cheackB.value === "N/A"){
                    naQuestion = naQuestion + 1;
                }
            }
        });
    });
    questions = questions - naQuestion;
    this.porcentaje = (affirmativeQuestion*100)/questions;
    changeColorSemaphore(porcentaje)
}






function changeColorSemaphore(){

    console.log(1 >= 50 && this.porcentaje < 100);

    const luzRoja = document.querySelector('.luz-roja');
    const luzAmarilla = document.querySelector('.luz-amarilla');
    const luzVerde = document.querySelector('.luz-verde');
    if (this.porcentaje < 50) {
        luzRoja.style.backgroundColor = 'red';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'gray';
    } else if (this.porcentaje >= 50 && this.porcentaje < 100) {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'yellow';
        luzVerde.style.backgroundColor = 'gray';
    } else {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'green';
    }
}


