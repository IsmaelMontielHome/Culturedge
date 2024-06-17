const topics = {
  getBoxIcons: async function () {
    const icons = await fetch('https://boxicons.com/_next/data/DNHfRjsQaNmPABrOMCg7E/index.json', {
      method: 'GET',
    }).then(response => response.json())
    .then(data => {
      return data.pageProps.icons;
    }).catch(error => console.error('Error getting boxiconsr icons:', error));
    
    const solidIcons = icons
      .filter(icon => icon.type_of_icon === "SOLID" || icon.type_of_icon === "REGULAR")
      .map(icon => ({
        name: icon.name,
        type: icon.type_of_icon === "SOLID" ? "bxs" : "bx",
      }));

    return solidIcons;
  },

  editConstructor: async function (data) {
    let html = `
      <form id="captureTopicData" onsubmit="topics.submitFormTopic(event)">
        <div class="principal-form">
          <div class="right-form">
            <div class="user-input">
              <label for="theme">Topic</label>
              <input type="text" class="profile_input" id="theme" name="theme" value="${data.theme || ''}">
            </div>
            <div class="user-input" id="drawer-container">
              <label for="icon" onclick="topics.boxIconDrawer()">
                Icon
              </label>
              <button class="profile_input boxicon-drawer-button" type="button" onclick="topics.boxIconDrawer()">
                <p>${data.icon || 'Select an Icon'}</p>
                <i class="${data.icon || 'bx bx-grid'} selected"></i>
                <i class="bx bx-chevron-down selector"></i>
              </button>
              <input type="hidden" id="icon" name="icon" value="${data.icon || 'bx bx-grid'}">
              <div id="drawer" class="boxicon-drawer hidden">
    `;
    
    const boxIcons = await this.getBoxIcons();
    
    boxIcons.forEach(boxIcon => {
      html += `
        <a class="boxicon">
          <i class="bx ${boxIcon.type}-${boxIcon.name}" onclick="topics.boxIconChooser(event)"></i>
        </a>
      `;
    });

    html += `
              </div>
            </div>
            <input type="hidden" name="id" value="${data.id || ''}">
          </div>
        </div>
      </form>
    `;

    return html;
  },

  boxIconDrawer: function () {
    const container = $('#drawer-container');
    const drawer = $('#drawer');
    const selector = $('i.selector');
    selector.hasClass('bx-chevron-down') ? selector.removeClass('bx-chevron-down').addClass('bx-chevron-up') : selector.removeClass('bx-chevron-up').addClass('bx-chevron-down');
    drawer.toggleClass('hidden');
    container.toggleClass('boxicon-drawer-container');
  },

  boxIconChooser: function (e) {
    e.preventDefault();

    const iconClassName = e.target.className;
    const iconButton = $('.boxicon-drawer-button');
    const iconInput = $('#icon');

    iconInput.val(iconClassName);
    iconButton.find('p').text(iconClassName);
    iconButton.find('.selected').attr('class', iconClassName + ' selected');

    this.boxIconDrawer();
  },

  edit: function (e, id) {
    e.preventDefault();


    Swal.fire({
      title: 'Loading...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      didOpen: () => {
          Swal.showLoading();
      }
    });
    
    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/unesco/get_theme', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        this.editConstructor(result.data)
          .then(form => {
            Swal.hideLoading();
            Swal.fire({
              title: `${result.data.theme}`,
              html: form,
              showCancelButton: true,
              confirmButtonText: 'Save',
              cancelButtonText: 'Cancel',
              customClass: {
                actions: 'modals-actions',
              }
            }).then((result) => {
              if (result.isConfirmed) {
                $('#captureTopicData').submit();
              } else {
                Swal.close();
              }
            });
          }).catch(error => {
            Swal.hideLoading();
            console.error('Error:', error)
          });
      } else {
        Swal.hideLoading();
        Swal.fire({
          icon: 'error',
          title: 'Error Getting the Topic data',
          text: result.message,
        });
      }
    }).catch(error => {
      Swal.hideLoading();
      console.error('Error:', error)
    });
  },

  create: function (e) {
    e.preventDefault();

    this.editConstructor({})
      .then(form => {
        Swal.fire({
          title: 'New Topics',
          html: form,
          showCancelButton: true,
          confirmButtonText: 'Save',
          cancelButtonText: 'Cancel',
          customClass: {
            actions: 'modals-actions',
          }
        }).then((result) => {
          if (result.isConfirmed) {
            $('#captureTopicData').submit();
          } else {
            Swal.close();
          }
        });
      }).catch(error => console.error('Error:', error));
  },

  submitFormTopic: async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    let requestURL = '';

    if (formData.get('id') === '') {
      requestURL = '/unesco/create';
    } else {
      requestURL = '/unesco/patch';
    }

    fetch(requestURL, {
      method: 'POST',
      body: formData,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        Swal.fire({
          icon: 'success',
          title: 'Topic Updated',
          text: result.message,
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error Updating the Topic',
          text: result.message,
        }).then(() => {
          location.reload();
        });
      }
    }).catch(error => {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'There was an error',
        text: error,
      }).then(() => {
        location.reload();
      });
    });
  },

  delete: function (e, id) {
    e.preventDefault();

    let params = new URLSearchParams();
    params.append('id', id);

    Swal.fire({
      icon: 'warning',
      title: 'Are you sure?',
      text: 'You will not be able to recover this topic!',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('/unesco/destroy', {
          method: 'POST',
          body: params,
        }).then(response => response.json())
        .then(result => {
          if (result.status) {
            Swal.fire({
              icon: 'success',
              title: 'Topic Deleted',
              text: result.message,
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error Deleting the Topic',
              text: result.message,
            }).then(() => {
              location.reload();
            });
          }
        }).catch(error => console.error('Error:', error));
      } else {
        Swal.close();
      }
    });
  },
}
