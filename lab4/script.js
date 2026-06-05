const display = document.querySelector('#display');
const clearButton = document.querySelector('#clear');
const backspaceButton = document.querySelector('#backspace');
const valueButtons = document.querySelectorAll('[data-value]');

function insertValue(value) {
    const start = display.selectionStart;
    const end = display.selectionEnd;

    display.value =
        display.value.slice(0, start) +
        value +
        display.value.slice(end);

    const newPosition = start + value.length;

    display.focus();
    display.setSelectionRange(newPosition, newPosition);
}

valueButtons.forEach((button) => {
    button.addEventListener('click', () => {
        insertValue(button.dataset.value);
    });
});

clearButton.addEventListener('click', () => {
    display.value = '';
    display.focus();
});

backspaceButton.addEventListener('click', () => {
    const start = display.selectionStart;
    const end = display.selectionEnd;

    if (start !== end) {
        display.value = display.value.slice(0, start) + display.value.slice(end);
        display.setSelectionRange(start, start);
    } else if (start > 0) {
        display.value = display.value.slice(0, start - 1) + display.value.slice(start);
        display.setSelectionRange(start - 1, start - 1);
    }

    display.focus();
});

display.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        display.value = '';
    }
});