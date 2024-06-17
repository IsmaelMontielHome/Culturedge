let inputFileFiles = {};

fetchUnescoThemes = () => {
  const unescoPicker = $('#unesco_theme_id');
  let themePicked = unescoPicker.data('theme');

  let html = '<option value="" disabled selected>Select a theme</option>'

  let params = new FormData();
  params.append('limit', 20);

  fetch('/unesco/get_themes', {
    method: 'POST',
    body: params,
  }).then(response => response.json())
  .then(result => {
    if (result.status) {
      if (themePicked) {
        html = '';
      }

      for (let item of result.data) {
        if (themePicked !== item.theme) {
          html += `<option data-icon="${item.icon}" value="${item.id}">${item.theme}</option>`;
        } else {
          html += `<option data-icon="${item.icon}" value="${item.id}" selected>${item.theme}</option>`;
        }
      }
    }

    unescoPicker.html(html);
  }).catch(err => console.error(err));
}

enableImagesCounter = (modalId) => {
  let imagesCounter = $('#images_count');

  if (!imagesCounter.length) {
    let container = $('#files-container');

    let imagesCount = $('.image-edit').length;
    let text = `View Image${imagesCount > 1 ? `s ${imagesCount}` : ''}`;

    container.append(`
      <input type="button" class="btn" id="images_count" onclick="openModal(${modalId})" value="${text}">
    `);
  } else {
    let imagesCount = $('.image-edit').length;
    $('#images_count').val('View Image' + (imagesCount > 1 ? `s ${imagesCount}` : ''));
  }
}

enableSliders = (modalId) => {
  let sliders = $('.prev');
  let imagesCount = $('.image-edit').length;
  
  if (imagesCount > 1 && !sliders.length) {
    let container = $('#myModal-' + modalId + ' .modal-content');

    let sliders = `
      <a class="prev" onclick="changeSlide(-1, ${modalId})">&#10094;</a>
      <a class="next" onclick="changeSlide(1, ${modalId})">&#10095;</a>
    `;

    container.append(sliders);
  }
}

deleteImage = (id, modalId) => {
  let params = new FormData();
  params.append('id', id);

  fetch('/posts/purge_image', {
    method: 'POST',
    body: params,
  }).then(response => response.json())
  .then(result => {
    if (result.status) {
      changeSlide(-1, modalId);
      $('#carouselSlide-' + id).remove();
      let imagesCount = $('.image-edit').length;
      $('#images_count').val('View Image' + (imagesCount > 1 ? `s ${imagesCount}` : ''));
    }
  }).catch(err => console.error(err));
}

removeFileFromInput = (fileInput, indexToRemove) => {
  const filesArray = Array.from(fileInput.files);
  filesArray.splice(indexToRemove, 1);
  const newFileList = new DataTransfer();
  filesArray.forEach(file => newFileList.items.add(file));
  fileInput.files = newFileList.files;
}

deleteImageFromCarousel = (id, modalId) => {
  $('#carouselSlideAdd-' + id).remove();
  changeSlide(-1, modalId);
  removeFileFromInput(inputFileFiles, id);

  let imagesCount = $('.image-edit').length;
  $('#images_count').val('View Image' + (imagesCount > 1 ? `s ${imagesCount}` : ''));

  if (imagesCount === 0) {
    closeModal(modalId);
    $('#images_count').remove();
    return;
  } else if (imagesCount === 1) {
    $('.prev').remove();
    $('.next').remove();
  }
}

deleteImagesFromCarousel = () => {
  let images = $('.image-add');

  if (images.length > 0) {
    images.remove();
    let imagesCount = $('.image-edit').length;
    $('#images_count').val('View Image' + (imagesCount > 1 ? `s ${imagesCount}` : ''));
  }
}

uploadedImage = (event, modalId) => {
  let fileInput = event.target;
  
  if (fileInput.files.length > 0) {
    inputFileFiles = fileInput;

    deleteImagesFromCarousel();
    
    for (let [i, file] of Object.entries(fileInput.files)) {
      let reader = new FileReader();
      reader.onload = function(e) {
        let image = `
          <div class="carousel-slide image-edit image-add" id="carouselSlideAdd-${i}">
            <img src="${e.target.result}" class="carousel-image" alt="Image to the post ${modalId}">
            <a class="remove-image" onclick="deleteImageFromCarousel('${i}', ${modalId})">
              <span>&times;</span>
            </a>
          </div>
        `;
        
        $('#carouselContainer-' + modalId).append(image);

        enableImagesCounter(modalId);
        enableSliders(modalId);
      }

      reader.readAsDataURL(file);
    };
  }
};

$(function () {
  fetchUnescoThemes();
});
