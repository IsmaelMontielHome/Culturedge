const user = {
  routes: {
    follow: '/users/follow',
    unfollow: '/users/unfollow',
  },
  
  containers: {
    followers: $('#followers-container'),
    following: $('#following-container'),
    mainSection: $('#mainSection'),
    editProfileModal: $('#editProfile'),
  },

  follow: function (e, form) {
    e.preventDefault();
    e.stopPropagation();

    let data = new FormData(form);
    let params = new URLSearchParams(data);

    fetch(this.routes.follow, {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: result.message,
          didClose: () => window.location.reload()
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    })
  },

  unfollow: function (e, form) {
    e.preventDefault();
    e.stopPropagation();

    let data = new FormData(form);
    let params = new URLSearchParams(data);

    fetch(this.routes.unfollow, {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: result.message,
          didClose: () => window.location.reload(),
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    })
  },

  followers: function (e) {
    e.preventDefault();
    let container = this.containers.followers;
    let id = app.params.id;

    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/users/followers', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        container.html(this.setFollowsList(result.data));
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    }).catch(err => console.error(err));
  },

  following: function (e) {
    e.preventDefault();
    let container = this.containers.following;
    let id = app.params.id;

    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/users/following', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        container.html(this.setFollowsList(result.data));
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    }).catch(err => console.error(err));
  },

  setFollowsList: function (users) {
    let list = `
      <div>
        <h1>Users</h1>
        <ul>
    `;

    if (users.length > 0) {
      for (let user of users) {
        list += `
          <li>
            <h3>${user.username}</h3>
            <a href="/users/show/id:${user.id}">View Profile</a>
          </li>
        `;
      }
    } else {
      list += `
        <li>
          <h2>No users found</h2>
        </li>
      `;
    }

    list += `
        </ul>
      </div>
    `;

    return list;
  },

  mainSectionActive: function (e) {
    let element = e.target;
    let parent = element.parentElement.parentElement;

    linkTags = parent.querySelectorAll('a');
    linkTags.forEach(tag => tag.classList.remove('active'));
    element.classList.add('active');
  }, 

  posts: function (e = null) {
    if (e) {
      this.mainSectionActive(e);
    }

    let container = this.containers.mainSection;
    let id = app.params.id;

    container.html(`
      <div class="loader">
        <div class="spinner spinner-green"></div>
      </div>
    `);

    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/users/posts', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        container.html(this.postsConstructor(result.data));
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    }).catch(err => console.error(err));
  },

  postsConstructor: function (posts) {
    let html = '';
    let avatarHtml;
      if (app.user.avatar) {
        avatarHtml = `<img src="/assets/imgs/${app.user.avatar}" class="user-card-img alt="${app.user.username}">`;
      } else {
        avatarHtml = `<img src="/resources/img/user.png" class="user-card-img alt="User">`;
      }

    if (posts.length > 0) { 
      console.log(posts)
      html += '<div>'
      for (let post of posts) {
        if (post.permission == 3){
        html += `
        <div class="hoverbox">
          <div class="box" id="results-list">
            <div class="user_card">
              <p class="user_card-info">
              ${avatarHtml}
                <div>
                  <p class="profile-card">${post.username.substring(0, 10)}</p>
                  <p class="date">${post.created_at}</p>
                </div>
              </p>
              <p class="user_card-post_theme">
                <a href="#">
                  <i class="${post.theme_icon}"></i>
                  ${post.theme}
                </a>
              </p>
            </div>
            <div class="line"></div>
            <div class="vi">
              <div> 
                <div id="results-list-${post.id}">
                  <div class="text">
                    <h2>${post.title}</h2>
                    <p class="text-description">${post.description}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="image-preview">
        `;

        if (post.images.length > 0) {
          html += `
            <div class="image">
              <img src="/assets/imgs/${post.images[0].image}" alt='Image from "${post.title}"' onclick="openModal(${post.id})">
            </div>
          `;
          if (post.images.length > 1) {
            html += `
              <div class="image-overlay" onclick="openModal(${post.id})">
                +${post.images.length - 1}
              </div>
            `;
          }
        }

        html += `
          </div>
          <div>
            <div class="actions">
              <div class="info">
                <a href="/posts/show/id:${post.id}">
                  <p class="likes ${app.user ? '' : 'openModal'}" id="reactions-count-${post.id}">
                    <img src="/resources/img/like.png" alt="like">${post.total_reactions} reactions
                  </p>
                </a>
              </div>
              <div class="info">
                <a href="/posts/show/id:${post.id}">
                  <p>${post.total_comments || 0} comments</p>
                </a>
              </div>
            </div>
          </div>
        `;

        if (app.user) {
          html += `
            <div class="all-reaction" id="react_${post.id}">
              <img src="/resources/img/thumb.gif" class="reaction" id="thumb_${post.id}"> 
              <img src="/resources/img/haha.gif" class="reaction" id="haha_${post.id}">
              <img src="/resources/img/love.gif" class="reaction" id="love_${post.id}">
              <img src="/resources/img/wow.gif" class="reaction" id="wow_${post.id}">
              <img src="/resources/img/sad.gif" class="reaction" id="sad_${post.id}">
              <img src="/resources/img/angry.gif" class="reaction" id="angry_${post.id}">
            </div>
          `;
        }

        html += `
          <div class="line"></div>
          <div class="actions">
            <div class="react-con" align="center" id="${post.id}">
        `;

        if (!!app.user.id && post.user_reactions) {
          html += `
            <img src="/resources/img/${post.user_reactions}.png" class="reaction-selected">
          `;
        } else {
          html += `
            <p><i class='bx bxs-like' onclick='app.checkSession()'></i></p>
          `;
        }

        html += `
                </div>
                <a href="/posts/show/id:${post.id}">
                  <p><i class='bx bxs-chat'></i> Comment</p>
                </a>
              </div>
            </div>
          </div>
        `;

        html += `
          <div id="myModal-${post.id}" class="modal">
            <span class="close" onclick="closeModal(${post.id})">&times;</span>
            <div class="modal-content">
              <br><br><br><br>
              <div class="carousel-container" id="carouselContainer-${post.id}">
        `;

        for (let imageObj of post.images) {
          html += `
            <div class="carousel-slide">
              <img src="/assets/imgs/${imageObj.image}" class="carousel-image" alt='Image from "${post.title}"'>
            </div>
          `;
        }

        html += `
          </div>
        `;

        if (post.images.length > 1) {
          html += `
            <a class="prev" onclick="changeSlide(-1, ${post.id})">&#10094;</a>
            <a class="next" onclick="changeSlide(1, ${post.id})">&#10095;</a>
          `;
        }

        html += `
            </div>
          </div>
        `;
      }
      html += '</div>';
      }
    } else { 
      html += `
        <div class="no-content-message">
          <img src="/resources/img/user.svg" alt="No found">
          <div>
            <p>No posts found,</p>
      `;

      if (app.user.id == app.params.id) {
        html += `
          <a href="/posts/new">create a post</a>
        `;
      }

      html += `
          </div>
        </div>
      `;
    }

    return html;
  },

  media: function (e) {
    this.mainSectionActive(e);

    let container = this.containers.mainSection;
    let id = app.params.id;

    container.html(`
      <div class="loader">
        <div class="spinner spinner-green"></div>
      </div>
    `);

    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/users/media', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        container.html(this.mediaConstructor(result.data));
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    }).catch(err => console.error(err));
  },

  mediaConstructor: function (media) {
    let imgs = '';
    let html = '';
    
    if (media.length > 0) {
      imgs = media.flatMap(post =>
        post.images.map(
          imageObj => `
            <a href="/posts/show/id:${post.post_id}">
              <img src="/assets/imgs/${imageObj.image}" alt='Image from "${imageObj.post_title}"'>
            </a>
          `
        )
      )

      if (imgs.length > 0) {
        html += `
          <div class="photos">
            ${imgs.join('')}
          </div>
        `;
      } else {
        html += `
          <div class="no-content-message">
            <img src="/resources/img/user.svg" alt="No found">
            <div>
              <p>No images found.</p>
        `;
  
        if (app.user.id == app.params.id) {
          html += `
            <a href="/posts/new">create a post</a>
          `;
        }
  
        html += `
            </div>
          </div>
        `;
      }
    } else {
      html += `
        <div class="no-content-message">
          <img src="/resources/img/user.svg" alt="No found">
          <div>
            <p>No images found.</p>
      `;

      if (app.user.id == app.params.id) {
        html += `
          <a href="/posts/new">create a post</a>
        `;
      }

      html += `
          </div>
        </div>
      `;
    }

    return html;
  },

  comments: function (e) {
    this.mainSectionActive(e);

    let container = this.containers.mainSection;
    let id = app.params.id;

    container.html(`
      <div class="loader">
        <div class="spinner spinner-green"></div>
      </div>
    `);

    let params = new URLSearchParams();
    params.append('id', id);

    fetch('/users/comments', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        container.html(this.commentsConstructor(result.data));
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message
        })
      }
    }).catch(err => console.error(err));
  },

  commentsConstructor: function (comments) {
    html = '';

    if (comments.length > 0) {
      html += `
        <div class="section-comments">
      `;

      for (let comment of comments) {
        html += `
          <a href="/posts/show/id:${comment.post_id}">
            <div class="_comment">
              <div class="comment-content">
                  <p class="comment-comment">${comment.comment}</p>
              </div>
              <div class="reply-comment">
                  <p class="date-comment">${comment.created_at}</p>
              </div>
            </div>
          </a>
        `;
      }

      html += `
        </div>
      `;
    } else {
      html += `
        <div class="no-content-message">
          <img src="/resources/img/user.svg" alt="No found">
          <div>
            <p>No comments found.</p>
      `;

      if (app.user.id == app.params.id) {
        html += `
          <a href="/">Share your thoughts</a>
        `;
      }

      html += `
          </div>
        </div>
      `;
    }

    return html;
  },

  myProfile: async function (e) {
    e.preventDefault();

    let params = new URLSearchParams();
    params.append('id', app.user.id);

    let r = await fetch('/users/get_data', {
      method: 'POST',
      body: params,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        return result.data;
      } else {
        return new Object();
      }
    });

    let html = this.myProfileConstructor(r);

    this.editProfile(html);
  },

  myProfileConstructor: function (data) {
    html = `
      <form enctype="multipart/form-data" id="captureUserData" onsubmit="user.submitEditProfile(event)">
        <p>Edit profile</p>
        <div class="photo_portail">
          <img id="bannerImage" src="${data.banner ? '/assets/imgs/' + data.banner : '/resources/img/bg.jpeg'}"/>
          <div class="change-images">
            <button id="changeImageBanner" onclick="user.changeBanner(event)">
              <i class="bi bi-camera-fill"></i> Change image
            </button>
            <input type="file" id="banner" accept="image/*" name="banner">
          </div>
        </div>
        <p class="instruction-modal">Photo profile</p>
        <div class="principal-form">
          <div class="left-form">
            <div class="photo_profile">
              <img id="profileImage" src="${data.avatar ? '/assets/imgs/' + data.avatar : '/resources/img/user.png'}"/>
              <div class="change-images">
                <button id="changeImageProfile" onclick="user.changeAvatar(event)">
                  <i class="bi bi-camera-fill"></i>
                </button>
                <input type="file" id="pfp" accept="image/*" name="pfp">
              </div>
            </div>
          </div>
          <div class="right-form">
            <div class="user-input">
              <label for="gender">Gender</label>
              <input class="profile_input" type="text" id="gender" name="gender" value="${data.gender || ''}">
            </div>
            <div class="user-input">
              <label for="birthdate">Birthdate</label>
              <input type="date" min="1900-01-01" max="2024-12-31" class="profile_input" id="birthdate" name="birthdate" value="${data.birthdate || ''}">
            </div>
            <input type="hidden" name="user_id" value="${app.user.id}">
          </div>
        </div>
      </form>
    `;

    return html;
  },

  editProfile: function (html) {
    Swal.fire({
      html: html,
      customClass: {
        container: 'modal-contents',
        actions: 'action-forms',
        confirmButton: 'save-btn',
        denyButton: 'cancel-btn',
      },
      showConfirmButton: true,
      showDenyButton: true,
      confirmButtonText: 'Save',
      denyButtonText: 'Cancel',
    }).then((result) => {
      if (result.isConfirmed) {
        $('#captureUserData').submit();
      } else {
        Swal.close();
      }
    });
  },

  changeAvatar: function (e) {
    e.preventDefault();
    e.stopPropagation();

    const fileProfile = $('#pfp');
    const profileImage = $('#profileImage');

    fileProfile.click()
    fileProfile.change(() => {
      const file = fileProfile[0].files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          profileImage.attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      } else {
        profileImage.attr('src', '/resources/img/user.png');
      }
    });
  },

  changeBanner: function (e) {
    e.preventDefault();
    e.stopPropagation();

    const fileBanner = $('#banner');
    const bannerImage = $('#bannerImage');

    fileBanner.click()
    fileBanner.change(() => {
      const file = fileBanner[0].files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          bannerImage.attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
      } else {
        bannerImage.attr('src', '/resources/img/bg.jpeg');
      }
    });
  },

  submitEditProfile: async function (e) {
    e.preventDefault();
    
    let form = e.target;
    let data = new FormData(form);
    let requestURL = '';

    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    console.log('fetching has_data');
    await fetch('/users/has_data', {
      method: 'POST',
      body: data,
    }).then(response => response.json())
    .then(result => {
      if (result.status) {
        requestURL = '/users/update_user_data';
      } else {
        requestURL = '/users/save_user_data';
      }
    }).catch(err => {
      console.error(err);
      Swal.hideLoading();
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err,
        didClose: () => window.location.reload(),
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.reload();
        }
      });
    });
    console.log('fetching update_user_data');
    fetch(requestURL, {
      method: 'POST',
      body: data,
    }).then(response => response.json())
    .then(result => {
      Swal.hideLoading();
      if (result.status) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: result.message,
          didClose: () => window.location.reload(),
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: result.message,
          didClose: () => window.location.reload(),
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.reload();
          }
        });
      }
    }).catch(err => {
      Swal.hideLoading();
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err,
        didClose: () => window.location.reload(),
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.reload();
        }
      });
    });
  }
}

$(function () {
  user.posts();
});
