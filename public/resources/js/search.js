function debounce(func, delay) {
  let timeout;
  return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

function searchOnInput() {
    const query = document.getElementById('search-input').value.trim();
    const resultsContainer = document.getElementById('search-results');
  
    if (query.length === 0) {
        resultsContainer.innerHTML = '';
        return;
    }
  
    const params = new URLSearchParams();
    params.append('query', query);
  
    fetch('/posts/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        resultsContainer.innerHTML = '';
        if (data.error) {
            resultsContainer.innerHTML = `<div class="not-found box-result"><p>${data.error}</p></div>`;
        } else if (data.length === 0) {
            resultsContainer.innerHTML = '<div class="not-found box-result"><p>No found result.</p></div>';
        } else {
            if (query.startsWith('@')) {
                data.forEach(user => {
                    const userHtml = `
                    <div class="box-result">
                        <a href="/users/show/id:${user.id}">
                            <div class="user_card">
                                <img src="/resources/img/user.png" alt="user" class="user-card-img">
                                <p class="profile-card">${user.username}</p>
                            </div>
                        </a>
                    </div>
                    `;
                    resultsContainer.innerHTML += userHtml;
                });
            } else {
                data.forEach(post => {
                    const postHtml = `
                    <div class="box-result" id="results-list">
                        <a href="/users/show/id:${post.user_id}">
                            <div class="user_card">
                                <img src="/resources/img/user.png" alt="user" class="user-card-img">
                                <p class="profile-card"><p>${post.username}</p></p>
                            </div>
                        </a>
                        <div class="vi">
                            <a href="/posts/show/id:${post.id}">
                                <div class="" id="results-list-${post.id}">
                                    <p class="text-theme"><i class="${post.theme_icon}"></i> ${post.theme}</p>
                                    <div class="text">
                                        <h2>${post.title}</h2>
                                        <div class="actions">
                                            <div class="info">
                                                <a href="/posts/show/id:${post.id}">
                                                    <center>
                                                        <p id="imagenDinamica"><i class='bx bx-show-alt'></i> Vision</p>
                                                    </center>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    `;
                    resultsContainer.innerHTML += postHtml;
                });
            }
        }
    })
    .catch(error => {
        console.error('Error fetching search results:', error);
    });
}
