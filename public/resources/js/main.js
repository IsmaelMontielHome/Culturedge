document.addEventListener('DOMContentLoaded', function() {
    var mainNav = document.getElementById('main-nav');
    var mainSection = document.querySelector('main');
    
    function adjustMargin() {
      if (window.innerWidth < 800 && !!mainSection) {
        mainSection.style.margin = '0';
      } else if (!!mainSection) {
        mainSection.style.margin = '78px 25% 0 12%';
      }
    }

    var menuState = localStorage.getItem('menuVisible');
    if (menuState === 'hidden' && !!mainNav) {
        mainNav.classList.add('hide-nav');
        if (window.innerWidth >= 800 && !!mainSection) { 
            mainSection.style.margin = '78px 25% 0 0%';
        }
    } else if (menuState === 'visible' && !!mainNav) {
        mainNav.classList.remove('hide-nav');
        if (window.innerWidth >= 800 && !!mainSection) { 
            mainSection.style.margin = '78px 25% 0 12%';
        }
    }

    window.addEventListener('resize', adjustMargin);
});

toggleHideNav = (e) => {
  e.preventDefault();
  var mainNav = document.getElementById('main-nav');
  var mainSection = document.querySelector('main');

  mainNav.classList.toggle('hide-nav');
  if (window.innerWidth >= 800) {
    if (mainNav.classList.contains('hide-nav')) {
      mainSection.style.margin = '78px 25% 0 2%';
    } else {
      mainSection.style.margin = '78px 25% 0 12%';
    }
    localStorage.setItem('menuVisible', mainNav.classList.contains('hide-nav') ? 'hidden' : 'visible');
  }
}

function autoSize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function toggleExtraImages() {
    const extraImages = document.querySelectorAll('.image img:nth-child(n+4)');
    extraImages.forEach(image => {
        image.style.display = image.style.display === 'none' ? 'block' : 'none';
    });
}

function toggleModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = modal.style.display === "block" ? "none" : "block";
}

function openModal(postId) {
    const modal = document.getElementById(`myModal-${postId}`);
    modal.style.display = "block";
    currentSlide(1, postId);
}

function closeModal(postId) {
    const modal = document.getElementById(`myModal-${postId}`);
    modal.style.display = "none";
}

let slideIndex = {};

function currentSlide(n, postId) {
    showSlides(slideIndex[postId] = n, postId);
}

function changeSlide(n, postId) {
    showSlides(slideIndex[postId] += n, postId);
}

function showSlides(n, postId) {
    let i;
    let slides = document.querySelectorAll(`#carouselContainer-${postId} .carousel-slide`);
    if (n > slides.length) {
        slideIndex[postId] = 1;
    }
    if (n < 1) {
        slideIndex[postId] = slides.length;
    }

    slides.forEach((slide) => {
      slide.style.display = "none";
    });

    if (slides.length > 0) {
        slides[slideIndex[postId] - 1].style.display = "block";
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = "none";
    }
}
