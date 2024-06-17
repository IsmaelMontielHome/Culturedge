const popular = {
  containers: { 
    popular: $('#popular-posts')
  },

  loadPopular: function() {
    let container = popular.containers.popular;
    container.html(`
    <div class="chat">
      <div class="loader">
        <span class="spinner spinner-green"></span>
      </div>
    </div>
  `);

    let params = new URLSearchParams();
    params.append('limit', 5);
    fetch('/posts/populars_limit', {
      method: 'POST',
      body: params
    }).then(response => response.json())
    .then(result => {
      let html = '';
      if (result.status) {
        if (result.data.length !== 0)
          for (let post of result.data) {
            html += `
            <div class="chat">
              <img alt="perfil" class="perfil" src="/resources/img/user.png">
              <div class="info">
                <p class="info-title">${post.title.substring(0, 10)}</p>
                <p class="info-reactions">${post.total_reactions} Reactions</p>
                <p class="info-date">${post.created_at}</p>
              </div>
            </div>
            `;
          }
        else {
          html = `
          <div class="chat">
            <div class="no-found">
              <p class="name">No popular posts</p>
            </div>
          </div>
          `;
        }
      } else {
        html = `
        <div class="chat">
          <div class="no-found">
            <p class="name">No popular posts</p>
          </div>
        </div>
        `;
      }
      container.html(html);
    }).catch(error => console.error('Popolar Error:', error));
  }
}

$(function() {
  popular.loadPopular();
});
