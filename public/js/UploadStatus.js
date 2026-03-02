// Upload Status - Static display only

class UploadStatusManager {
  constructor() {

    this.statusBadge = document.querySelector('.status-badge');
    this.init();
  }

  init() {
  
    if (this.statusBadge) {
      this.statusBadge.textContent = 'Completed';
      this.statusBadge.className = 'status-badge status-completed';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new UploadStatusManager();
});

export { UploadStatusManager };
