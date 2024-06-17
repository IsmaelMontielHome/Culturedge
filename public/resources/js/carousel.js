function toggleShowModal() {
    var modal = document.getElementById("showModal");
    modal.style.display = modal.style.display === "block" ? "none" : "block";
}

function openShowModal(postId) {
    const modal = document.getElementById(`showModal-${postId}`);
    modal.style.display = "block";
    showCurrentSlide(1, postId);
}

function closeShowModal(postId) {
    const modal = document.getElementById(`showModal-${postId}`);
    modal.style.display = "none";
}

let showSlideIndex = {};

function showCurrentSlide(n, postId) {
    showSlides(showSlideIndex[postId] = n, postId);
}

function changeShowSlide(n, postId) {
    showSlides(showSlideIndex[postId] += n, postId);
}

function showSlides(n, postId) {
    let i;
    let slides = document.querySelectorAll(`#showCarouselContainer-${postId} .show-carousel-slide`);
    if (n > slides.length) { showSlideIndex[postId] = 1 }
    if (n < 1) { showSlideIndex[postId] = slides.length }
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[showSlideIndex[postId] - 1].style.display = "block";
}

window.onclick = function(event) {
    if (event.target.classList.contains('show-modal')) {
        event.target.style.display = "none";
    }
}
