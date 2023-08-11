//---------- DEFINICION DE VARIABLES
var porcentaje;

//---------- LISTENERS


document.addEventListener('DOMContentLoaded', () => { //importante esta seccion para que todos los listener funcione
    uncheck();
    attachListener();
});

//---------- FUNCIONES


/**
 * The function "uncheck" is used to uncheck other checkboxes in the same row when one checkbox is
 * checked.
 */
function uncheck() {
    const rows = document.querySelectorAll('tr');
    rows.forEach(row => {
        const checkboxGroups = row.querySelectorAll('.checkbox-group');
        checkboxGroups.forEach(group => {
            group.addEventListener('change', function () {
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
//-------------------------------
/**
 * The function evaluates the responses to a set of questions and calculates the percentage of
 * affirmative answers.
 */
function evaluate() {
    var questions;
    var affirmativeQuestion = 0;
    var naQuestion = 0;

    const rows = document.querySelectorAll('tr'); // Obtener todas las filas
    questions = rows.length - 1;
    rows.forEach(row => {
        const checkboxGroups = row.querySelectorAll('.checkbox-group');
        checkboxGroups.forEach(cheackB => {
            if (cheackB.checked) {
                if (cheackB.value === "YES") {
                    affirmativeQuestion = affirmativeQuestion + 1;
                } else if (cheackB.value === "N/A") {
                    naQuestion = naQuestion + 1;
                }
            }
        });
    });
    questions = questions - naQuestion;
    this.porcentaje = (affirmativeQuestion * 100) / questions;
    changeColorSemaphore(porcentaje)
    displayPercentage(porcentaje);
}
//-------------------------------

/**
 * The function changes the color of a semaphore based on a given percentage value.
 */
function changeColorSemaphore() {

    console.log(1 >= 50 && this.porcentaje < 100);

    const luzRoja = document.querySelector('.luz-roja');
    const luzAmarilla = document.querySelector('.luz-amarilla');
    const luzVerde = document.querySelector('.luz-verde');
    if (this.porcentaje < 50) {
        luzRoja.style.backgroundColor = 'red';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'gray';
        Swal.fire({
            icon: 'error',
            title: '¡Percentage is insecure!',
            text: 'Should be improved',
            showConfirmButton: false,
            timer: 1500 // El alert se cerrará automáticamente después de 2 segundos
        });
    } else if (this.porcentaje >= 50 && this.porcentaje < 100) {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'yellow';
        luzVerde.style.backgroundColor = 'gray';
        Swal.fire({
            icon: 'success',
            title: '¡Percentage is safe',
            text: 'But it can be improved',
            showConfirmButton: false,
            timer: 1500 // El alert se cerrará automáticamente después de 2 segundos
        });
    } else {
        luzRoja.style.backgroundColor = 'gray';
        luzAmarilla.style.backgroundColor = 'gray';
        luzVerde.style.backgroundColor = 'green';
        Swal.fire({
            icon: 'success',
            title: '¡Percentage is secure!',
            text: 'It is done',
            showConfirmButton: false,
            timer: 1500 // El alert se cerrará automáticamente después de 2 segundos
        });
    }
}
//-------------------------------
/**
 * The function attaches a click event listener to a button and calls the evaluate function when the
 * button is clicked.
 */
function attachListener() {
    const evaluateButton = document.querySelector('#evaluateButton');
    evaluateButton.addEventListener('click', () => {
        changeColorSemaphore(this.evaluate());
    });
}


function displayPercentage(percentage) {
    const percentageDisplay = document.querySelector('.percentage-display');
    percentageDisplay.textContent = `Safe percentage: ${percentage.toFixed(2)}%`;
}

//------------------------------- END OF SCRIPT