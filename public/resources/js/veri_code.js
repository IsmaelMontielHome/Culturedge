document.addEventListener('DOMContentLoaded', function () {
    const inputsContainer = document.getElementById('inputs');
    const codeCombinedInput = document.getElementById('code');
    const inputs = inputsContainer.querySelectorAll('.inputs');
    const inputn = ["input1", "input2", "input3", "input4", "input5", "input6"];

    inputn.forEach((id) => {
        const input = document.getElementById(id);
        addListener(input);
    });

    inputsContainer.addEventListener('input', function() {
        let codeCombined = '';
        inputs.forEach(function(input) {
            codeCombined += input.value.trim();
        });
        codeCombinedInput.value = codeCombined;
    });

    function addListener(input) {
        input.addEventListener("keyup", (event) => {
            const value = input.value;
            if (/[0-9a-z]/gi.test(value)) {
                const next = input.nextElementSibling;
                if (next) next.focus();
            } else {
                input.value = "";
            }
    
            const key = event.key;
            if (key === "Backspace" || key === "Delete") {
                const prev = input.previousElementSibling;
                if (prev) prev.focus();
            }
        });
    }
});
