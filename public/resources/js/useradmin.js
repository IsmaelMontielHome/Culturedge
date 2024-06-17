function confirmDelete() {
    return confirm('Are you sure you want to delete this user?');
}
function confirmDeletePost(event) {
    event.preventDefault(); // Previene el envío automático del formulario
  
    if (confirm('Are you sure you want to delete this post?')) {
      event.target.submit(); // Envía el formulario si el usuario confirma
    }
  }
