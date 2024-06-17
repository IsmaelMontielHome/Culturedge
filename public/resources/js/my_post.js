const myPost = {

    containers: {
        mainSection: $('#mainSection'),
      },

    mainSectionActive: function (e) {
        let element = e.target;
        let parent = element.parentElement.parentElement;
    
        linkTags = parent.querySelectorAll('a');
        linkTags.forEach(tag => tag.classList.remove('active'));
        element.classList.add('active');
      }, 

    review: function (e = null) {
        if (e) {
          this.mainSectionActive(e);
        }
    
        let container = this.containers.mainSection;
        let id = app.user.id;
    
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
            container.html(this.reviewConstructor(result.data));
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: result.message
            })
          }
        }).catch(err => console.error(err));
    },
    
    reviewConstructor: function (posts) {
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
        if (post.permission == 1){
        html += `
        <div class="hoverbox">
            <div class="box" id="results-list">
                <div class="user_card between">
                    <button class="buttonRed" onclick="window.location.href='/posts/edit/id:${post.id}'">
                        <span class="text"><i class="bi bi-pencil"></i></span>
                    </button>
                    <form method="POST" action="/posts/drop" onsubmit="return confirmDeletePost(event);">
                                <input type="hidden" value="${post.id}" name="id" id="id-${post.id}">
                                <button class="buttonBlue" type="submit">
                                <span class="text"><i class="bi bi-trash"></i></span>
                                </button>
                    </form>
                </div>
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
        `;

        html += `
            <div class="line"></div>
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

    rejected: function (e) {
    this.mainSectionActive(e);

    let container = this.containers.mainSection;
    let id = app.user.id;

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
        container.html(this.rejectedConstructor(result.data));
        } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message
        })
        }
    }).catch(err => console.error(err));
    },

    rejectedConstructor: function (posts) {
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
            if (post.permission == 2){
                html += `
                    
                <div class="hoverbox">
                    <div class="box" id="results-list">
                    <div class="user_card">
                        <h4>Review: </h4>
                        <p>${post.reason}</p>
                    </div>
                    <div class="user_card between">

                        <button class="buttonRed" onclick="window.location.href='/posts/edit/id:${post.id}'">
                            <span class="text"><i class="bi bi-pencil"></i></span>
                        </button>
                        <form method="POST" action="/posts/drop" onsubmit="return confirmDeletePost(event);">
                                <input type="hidden" value="${post.id}" name="id" id="id-${post.id}">
                                <button class="buttonBlue" type="submit">
                                <span class="text"><i class="bi bi-trash"></i></span>
                                </button>
                        </form>
                    </div>
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
                `;
        
                html += `
                    <div class="line"></div>
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

    accepted: function (e) {
    this.mainSectionActive(e);

    let container = this.containers.mainSection;
    let id = app.user.id;

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
        container.html(this.acceptedConstructor(result.data));
        } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message
        })
        }
    }).catch(err => console.error(err));
    },

    acceptedConstructor: function (posts) {
        let html = '';
        let avatarHtml;
      if (app.user.avatar) {
        avatarHtml = `<img src="/assets/imgs/${app.user.avatar}" class="user-card-img alt="${app.user.username}">`;
      } else {
        avatarHtml = `<img src="/resources/img/user.png" class="user-card-img alt="User">`;
      }
        if (posts.length > 0) { 
            console.log(posts)
            html += `<div>`;
            for (let post of posts) {
                if (post.permission == 3){
                    html += `
                    <div class="hoverbox">
                        <div class="box" id="results-list">
                        <div class="user_card between">
                            <button class="buttonRed" onclick="window.location.href='/posts/edit/id:${post.id}'">
                                <span class="text"><i class="bi bi-pencil"></i></span>
                            </button>
                            <form method="POST" action="/posts/drop" onsubmit="return confirmDeletePost(event);">
                                <input type="hidden" value="${post.id}" name="id" id="id-${post.id}">
                                <button class="buttonBlue" type="submit">
                                <span class="text"><i class="bi bi-trash"></i></span>
                                </button>
                            </form>
                        </div>
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
                    `;
            
                    html += `
                        <div class="line"></div>
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
}
$(function () {
    myPost.review();
  });
  