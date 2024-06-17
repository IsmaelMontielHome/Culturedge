document.addEventListener('DOMContentLoaded', () => {
    const accButtons = document.getElementsByClassName('accordion-button');
    for (let i = 0; i < accButtons.length; i++) {
        accButtons[i].onclick = function() {
            this.classList.toggle('active');
            const content = this.nextElementSibling;
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const mainElement = document.querySelector('main');
    mainElement.style.margin = '5% 0% 0% 10% ';
});