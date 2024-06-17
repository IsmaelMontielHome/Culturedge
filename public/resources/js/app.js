const app = {
  uri: {},
  params: {},
  user: {},

  sleep: function(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  },
  
  get_uri: function() {
    let uri = window.location.pathname;

    if (uri === '/') {
      return '/';
    }

    uri = uri.ltrim('/').rtrim('/').split('/', 2).join('/');

    return `/${uri}/`;
  },

  get_params: function() {
    let uri = window.location.pathname;
    
    if (uri === '/') {
      return uri;
    }
    
    uri = uri.ltrim('/').rtrim('/').split('/').slice(2);

    if (uri.length === 0) {
      return uri;
    }

    uri = uri.map(item => item.split(':'))

    return Object.fromEntries(uri);
  },

  checkSession: function() {
    if (!app.user || !app.user.id) {
      Swal.fire({
        icon: 'info',
        title: 'You need to log in to perform this action.',
        footer: `
          <a href="/sessions/create" class="actions">
            <p style="margin: auto;">Click here to log in</p>
          </a>
        `,
        showConfirmButton: false,
        showCloseButton: true,
      });
    }
  },
  
  reviewPosts: function(id) {
    let params = new URLSearchParams();
    params.append('id', id);
    Swal.fire({
      didOpen: () => {
        Swal.showLoading();
        fetch('/posts/show_json/', {
          method: 'POST',
          body: params,
        }).then(response => response.json())
        .then(data => {
          Swal.hideLoading();
          let html = `
            <div class="adminMenu-header">
            <h1>REVIEW POST</h1>
              <div class="adminMenu-showpost">
                <div class="adminMenu-show">
                  <div class="user_card">
                      <img src="/resources/img/user.png" alt="user" class="user-card-img">
                      <div>
                        <p class="profile-card">${data.username}</p>
                        <p class="date">${data.created_at}</p>
                      </div>
                    <p class="user_card-post_theme">
                        <i class="${data.theme_icon}"></i>
                        ${data.theme}
                    </p>
                  </div>
                  <div class="line"></div>
                  <div>
                    <h2>${data.title}</h2>
                    <p>${data.description}</p>
                    <div class="images">`; 
                      if (data.images.length > 0) { 
                        html += `
                        <div class="image">
                          <img src="/assets/imgs/${data.images[0].image}" alt='Image from "${data.title}"' onclick="openModal(${data.id})">`;
                            if (data.images.length > 1) {
                              html += `
                              <div class="image-overlay" onclick="openModal(${data.id})">+${data.images.length - 1}</div>`;
                            }
                        html+= `
                        </div>`;
                      }
                      html += `
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="line"></div>
            <div class="">
              <button id="acceptButton" class="buttonGreen" onclick="app.acceptPost(${data.id})">
                <span class="text"><i class="bi bi-archive-fill"></i>Aceptar</span>
              </button>
              <button class="buttonRed" onclick="app.cancelPost(${data.id})">
                  <span class="text"><i class="bi bi-star-fill"></i>Decline</span>
              </button>
            </div>
            <div id="myModal-${data.id}" class="modal">
              <span class="close" onclick="closeModal(${data.id})">&times;</span>
              <div class="modal-content">
                <br><br><br><br>
                <div class="carousel-container" id="carouselContainer-${data.id}">`;
                  for (let imageObj of data.images) {
                    html += `
                    <div class="carousel-slide">
                      <img src="/assets/imgs/${imageObj.image}" class="carousel-image" alt='Image from "${data.title}"'>
                    </div>`;
                  }
                `</div>`;
                if (data.images.length > 1) {
                  html += `
                  <a class="prev" onclick="changeSlide(-1, ${data.id})">&#10094;</a>
                  <a class="next" onclick="changeSlide(1, ${data.id})">&#10095;</a>`
                }
                html += `
              </div>
            </div>
          `;
          Swal.update({
            html: html,
            showConfirmButton: false,
            customClass: {
              container: 'adminMenu',
              popup: 'adminMenu-modal',
            },
          });
        }).catch(error => {
          Swal.hideLoading();
          Swal.update({
            icon: 'error',
            title: 'An error occurred',
            text: `Error: ${error}`,
          });
        });
      }
    });
  },
  
  acceptPost: function(id) {
    let params = new URLSearchParams();
    params.append('id', id);
    Swal.fire({
      didOpen: () => {
        Swal.showLoading();
        fetch('/admins/accepted/', {
          method: 'POST',
          body: params,
        }).then(response => response.json())
        .then(data => {
          Swal.hideLoading();
          if (data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Post successfully accepted',
              showConfirmButton: false,
              timer: 1500
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error accepting the post',
              text: data.message
            });
          }
        }).catch(error => {
          Swal.hideLoading();
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `Error: ${error}`
          });
        });
      }
    });
  },
  
  cancelPost: function(id) {
    let params = new URLSearchParams();
    params.append('id', id);
    Swal.fire({
      didOpen: () => {
        Swal.showLoading();
        fetch('/posts/show_json/', {
          method: 'POST',
          body: params,
        }).then(response => response.json())
        .then(data => {
          console.log(data);
          Swal.hideLoading();
          Swal.update({
            html: `
              <form id="rejectionForm">
                <div class="userMenu-header">
                  <div>
                    <h4>Decline publication</h4>
                  </div>
                </div>
                <div class="line"></div>
                <div>
                  <p>Why is the publication being rejected?</p>
                  <textarea id="rejectionReason" rows="4" required></textarea>
                </div>
                <div class="line"></div>
                <div>
                  <button type="button" class="buttonRed" onclick="app.reviewPosts(${id})">
                    <span class="text"><i class="bi bi-star-fill"></i>Cancel</span>
                  </button>
                  <button type="submit" class="buttonGreen">
                    <span class="text"><i class="bi bi-star-fill"></i>Send</span>
                  </button>
                </div>
              </form>
            `,
            showConfirmButton: false,
            customClass: {
              container: 'adminMenu',
              popup: 'adminMenu-modal',
            },
          });

          document.getElementById('rejectionForm').addEventListener('submit', function(event) {
            event.preventDefault();
            app.sendRejection(id);
          });
        }).catch(error => {
          Swal.hideLoading();
          Swal.update({
            icon: 'error',
            title: 'An error occurred',
            text: `Error : ${error}`,
          });
        });
      }
    });
  },

  sendRejection: function(id) {
    const reason = document.getElementById('rejectionReason').value;
    let params = new URLSearchParams();
    params.append('id', id);
    params.append('reason', reason);

    Swal.showLoading();
    fetch('/admins/rejected', {
        method: 'POST',
        body: params,
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not successful');
        }
        return response.json();
    }).then(data => {
        Swal.hideLoading();
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Publication Rejected',
                text: 'The publication has been rejected successfully.',
              }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'An error occurred',
                text: `Error: ${data.message}`,
            });
        }
    }).catch(error => {
        Swal.hideLoading();
        Swal.fire({
            icon: 'error',
            title: 'An error occurred',
            text: `Error: ${error.message}`,
        });
    });
  },

  userMenuOpen: function() {
    let avatarHtml;
    if (app.user.avatar) {
      avatarHtml = `<img src="/assets/imgs/${app.user.avatar}" alt="${app.user.username}">`;
    } else {
      avatarHtml = `<img src="/resources/img/user.png" alt="User">`;
    }

    Swal.fire({
      html: `
        <div class="userMenu-header">
          ${avatarHtml}
          <h4>${app.user.username}</h4>
          <p>${app.user.email}</p>
        </div>
        <div class="line"></div>
        <ul class="userMenu-list">
          <li class="userMenu-list-item d-grid">
            <a class="text-start" href="/users/show/id:${app.user.id}">Profile</a>
          </li>
          <li class="userMenu-list-item d-grid">
            <a class="text-start" href="/sessions/destroy">Logout</a>
          </li>
        </ul>
      `,
      showConfirmButton: false,
      customClass: {
        container: 'userMenu',
        popup: 'userMenu-modal',
      },
    });
  },

  userNotificationsOpen: function() {
    Swal.fire({
      html: `
        <div class="userMenu-header">
          <h4>Notifications</h4>
        </div>
        <div class="line"></div>
        <ul class="userMenu-tabs">
          <li class="userMenu-tabs-item" id="unseenNotifications" onclick="app.unseenNotifications()">
            Unseen notifications
          </li>
          <li class="userMenu-tabs-item" id="allNotifications" onclick="app.allNotifications()">
            All notifications
          </li>
        </ul>
        <ul class="userMenu-list">
        </ul>
      `,
      showConfirmButton: false,
      customClass: {
        container: 'userMenu',
        popup: 'userMenu-modal',
      },
      didOpen: this.unseenNotifications,
    });
  },

  unseenNotifications: async function() {
    $('#unseenNotifications').addClass('active');
    $('#allNotifications').removeClass('active');

    let html = `
      <li class="userMenu-list-item spinner-container">
        <span class="spinner spinner-green"></span>
      </li>
    `;

    let container = $('.userMenu-list');
    let id = app.user.id;

    let params = new URLSearchParams();
    params.append('id', id);

    await fetch('/users/notifications', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      html = '';

      if (result.status && result.data.length > 0) {
        let notifications = result.data;

        for (let notification of notifications) {
          html += app.newNotification(notification);
        }

        container.html(html);

        app.markNotificationsAsRead(result.data);
      } else {
        container.html('<li class="userMenu-list-item-centered">Ooh! You don\'t have notifications</li>');
      }
    }).catch(err => console.error(err));

  },

  allNotifications: async function() {
    $('#allNotifications').addClass('active');
    $('#unseenNotifications').removeClass('active');

    let html = `
      <li class="userMenu-list-item spinner-container">
        <span class="spinner spinner-green"></span>
      </li>
    `;

    let container = $('.userMenu-list');
    let id = app.user.id;

    let params = new URLSearchParams();
    params.append('id', id);

    await fetch('/users/all_notifications', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      html = '';

      if (result.status && result.data.length > 0) {
        let notifications = result.data;

        for (let notification of notifications) {
          html += app.newNotification(notification);
        }

        container.html(html);
      } else {
        container.html('<li class="userMenu-list-item-centered">Ooh! You don\'t have notifications</li>');
      }
    }).catch(err => console.error(err));
  },

  newNotification: function(data) {
    let htmlLi = `
      <li class="userMenu-list-item notification">
          <a href="/users/show/id:${data.causer_id}" class="notification-causer col-8">
            <img src="/resources/img/user.png" alt="User">
            <div class="notification-causer-info">
              <h5>${data.username}</h5>
    `;
    
    switch (data.type) {
      case 'post':
        htmlLi += `
              <p>has posted just now</p>
            </div>
          </a>
          <a href="/posts/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'follow':
        htmlLi += `
              <p>has followed you</p>
            </div>
          </a>
          <a href="/users/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'like':
        htmlLi += `
              <p>has liked your post</p>
            </div>
          </a>
          <a href="/posts/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'comment':
        htmlLi += `
              <p>has commented your post</p>
            </div>
          </a>
          <a href="/posts/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'reply':
        htmlLi += `
              <p>has replied your comment</p>
            </div>
          </a>
          <a href="/posts/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'post_approved':
        htmlLi = `
          <li class="userMenu-list-item notification">
              <a href="/posts/my_posts" class="notification-causer col-8">
                <div class="notification-causer-info">
                  <h5>Post approved</h5>
                  <p>Your post has been approved and is now published</p>
                </div>
              </a>
              <a href="/posts/show/id:${data.id}" class="col-2 notification-event">
        `;
        break;
      case 'post_rejected':
        htmlLi = `
          <li class="userMenu-list-item notification">
              <a href="/posts/my_posts" class="notification-causer col-8">
                <div class="notification-causer-info">
                  <h5>Post rejected</h5>
                  <p>Go to the post to see the reason</p>
                </div>
              </a>
        `;
        break;
      case 'post_created':
        htmlLi = `
          <li class="userMenu-list-item notification">
              <a href="/posts/my_posts" class="notification-causer col-8">
                <div class="notification-causer-info">
                  <h5>Post created</h5>
                  <p>After review, your post will be published</p>
                </div>
              </a>
        `;
        break;
      default:
        console.error('Invalid type');
        break;
    }

    if (data.type === 'post_rejected' || data.type === 'post_created') {
      htmlLi += `
            <span class="col-2 notification-date">
              ${data.created_at}
            </span>
          </div>
        </li>
      `;
    } else {
      htmlLi += `
              <p>Go</p>
              <i class="bi ${data.seen ? 'bi-eye-slash-fill' : 'bi-eye-fill'}"></i>
            </a>
            <span class="col-2 notification-date">
              ${data.created_at}
            </span>
          </div>
        </li>
      `;
    }

    return htmlLi;
  },

  markNotificationsAsRead: function(data) {
    let ids = data.map(item => item.notification_id);
    
    let params = new URLSearchParams();
    ids.forEach(id => params.append('ids[]', id));

    fetch('/users/mark_notifications_as_read', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        app.unseenNotificationsCount();
      }
    }).catch(err => console.error(err));
  },

  unseenNotificationsCount: function() {
    let notificationCount =  $('.notifications-count')

    let params = new URLSearchParams();
    params.append('id', app.user.id);

    fetch('/users/notifications_count', {
      method: 'POST',
      body: params,
    })
    .then(response => response.json())
    .then(result => {
      if (result.status && result.data.notifications > 0) {
        let count = result.data.notifications;
        notificationCount.text(count);
      } else {
        notificationCount.css('display', 'none');
      }
    }).catch(err => console.error(err));
  },

  loadUnescoThemes: function(limit = 3) {
    let themesContainer = $('#unesco-themes');
    let html = `
      <li class="list">
        <div class="selector"></div>
        <a class="category">
          <p>Error 
        </a>
      </li>
    `;

    let params = new URLSearchParams();
    params.append('limit', limit);

    fetch('/unesco/get_themes', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        html = '';

        for (let item of result.data) {
          html += `
            <li class="list">
              <div class="selector"></div>
              <a class="category" href="/posts/unesco/topic:${item.theme.split(' ').join('_')}">
                <i class="${item.icon}"></i>
                <p>${item.theme}</p>
              </a>
            </li>
          `;
        }

        if (limit === 3) {
          html += `
            <li class="list">
              <div class="selector"></div>
              <a class="category" onclick="app.loadAllUnescoThemes()">
                <i class="bi bi-three-dots"></i>
                <p>More</p>
              </a>
            </li>
          `;
        }
      }
      
      themesContainer.html(html);
    }).catch(err => console.error(err));
  },

  loadAllUnescoThemes: function() {
    this.loadUnescoThemes(20);
  }
};

String.prototype.rtrim = function(char) {
  return this.replace(new RegExp(char + '+$'), '');
}

String.prototype.ltrim = function(char) {
  return this.replace(new RegExp('^' + char + '+'), '');
}

app.uri = app.get_uri();
app.params = app.get_params();

$(function() {
  if (app.user.id) {
    app.unseenNotificationsCount();
  }

  app.loadUnescoThemes();
})
