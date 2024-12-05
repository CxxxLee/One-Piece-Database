function clearDisplay(){
    display.value = "";
}

const display = document.getElementById("display");
const MAX_DISPLAY_LENGTH = 12;

function appendToDisplay(input) {
    if (input === 'sqrt') {
        if (!isNaN(display.value) && display.value !== "") {
            display.value = Math.sqrt(parseFloat(display.value)).toString().slice(0, MAX_DISPLAY_LENGTH);
        }
    } else if (input === '+/-') {
        if (!isNaN(display.value) && display.value !== "") {
            display.value = (parseFloat(display.value) * -1).toString().slice(0, MAX_DISPLAY_LENGTH);
        }
    } else {
        if (display.value.length < MAX_DISPLAY_LENGTH) {
            display.value += input;
        }
    }
}

function calculate() {
    try {
        display.value = eval(display.value).toString().slice(0, MAX_DISPLAY_LENGTH); // Limit final result
    } catch (error) {
        display.value = "ERROR";
    }
}