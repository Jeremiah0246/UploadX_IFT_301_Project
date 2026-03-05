/**
 * UploadX File Manager
 * Drag & drop, upload, download, delete, search, preview
 */

let selectMode = false;

// DOM Elements
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const uploadProgress = document.getElementById('uploadProgress');
const progressBar = document.getElementById('progressBar');
const progressText = document.getElementById('progressText');
const searchInput = document.getElementById('searchInput');
const previewModal = document.getElementById('previewModal');

// ==================== DRAG & DROP ====================

uploadZone.addEventListener('click', (e) => {
    if (e.target === uploadZone || e.target.closest('.upload-icon, .upload-title, .upload-subtitle')) {
        fileInput.click();
    }
});

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFiles(files);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFiles(e.target.files);
    }
});

// ==================== FILE UPLOAD ====================

async function handleFiles(files) {
    const filesArray = Array.from(files);
    
    for (let i = 0; i < filesArray.length; i++) {
        await uploadFile(filesArray[i], i + 1, filesArray.length);
    }
    
    // Reset and reload
    fileInput.value = '';
    uploadProgress.classList.remove('active');
    
    showToast('Files uploaded successfully!', 'success');
    
    setTimeout(() => {
        window.location.reload();
    }, 1500);
}

async function uploadFile(file, current, total) {
    const formData = new FormData();
    formData.append('file', file);
    
    uploadProgress.classList.add('active');
    progressText.textContent = `Uploading ${current} of ${total}: ${file.name}`;
    
    try {
        const response = await fetch('upload-handler.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            showToast(data.message || 'Upload failed', 'error');
        }
        
        progressBar.style.width = (current / total * 100) + '%';
        
    } catch (error) {
        console.error('Upload error:', error);
        showToast('Upload failed. Please try again.', 'error');
    }
}

// ==================== TAB SWITCHING ====================

document.querySelectorAll('.file-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        const tabName = tab.getAttribute('data-tab');
        
        // Update active tab
        document.querySelectorAll('.file-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Show corresponding content
        document.getElementById('photosTab').style.display = tabName === 'photos' ? 'block' : 'none';
        document.getElementById('documentsTab').style.display = tabName === 'documents' ? 'block' : 'none';
        
        // Clear search
        searchInput.value = '';
        filterFiles();
    });
});

// ==================== VIEW TOGGLE ====================

document.querySelectorAll('.view-toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const view = btn.getAttribute('data-view');
        
        // Update active button
        document.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        // Update view
        document.querySelectorAll('.files-grid').forEach(grid => {
            if (view === 'list') {
                grid.style.gridTemplateColumns = '1fr';
            } else {
                grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(200px, 1fr))';
            }
        });
    });
});

// ==================== SEARCH & FILTER ====================

searchInput.addEventListener('input', filterFiles);

function filterFiles() {
    const query = searchInput.value.toLowerCase();
    const activeTab = document.querySelector('.file-tab.active').getAttribute('data-tab');
    const grid = activeTab === 'photos' ? document.getElementById('photosGrid') : document.getElementById('documentsGrid');
    
    if (!grid) return;
    
    const cards = grid.querySelectorAll('.file-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const fileName = card.getAttribute('data-name').toLowerCase();
        if (fileName.includes(query)) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const emptyState = grid.parentElement.querySelector('.empty-state');
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// ==================== SELECT MODE ====================

function toggleSelectMode() {
    selectMode = true;
    
    document.getElementById('selectBtn').style.display = 'none';
    document.getElementById('downloadBtn').style.display = 'inline-flex';
    document.getElementById('deleteBtn').style.display = 'inline-flex';
    document.getElementById('cancelBtn').style.display = 'inline-flex';
    
    document.querySelectorAll('.file-card').forEach(card => {
        card.classList.add('select-mode');
    });
}

function cancelSelectMode() {
    selectMode = false;
    
    document.getElementById('selectBtn').style.display = 'inline-flex';
    document.getElementById('downloadBtn').style.display = 'none';
    document.getElementById('deleteBtn').style.display = 'none';
    document.getElementById('cancelBtn').style.display = 'none';
    
    document.querySelectorAll('.file-card').forEach(card => {
        card.classList.remove('select-mode');
        card.querySelector('.file-card-checkbox').checked = false;
    });
}

// ==================== DOWNLOAD ====================

async function downloadSelected() {
    const checkboxes = document.querySelectorAll('.file-card-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showToast('Please select files to download', 'error');
        return;
    }
    
    for (let checkbox of checkboxes) {
        await downloadFile(checkbox.value);
        await new Promise(resolve => setTimeout(resolve, 500)); // Delay between downloads
    }
    
    showToast(`${checkboxes.length} file(s) downloaded`, 'success');
    cancelSelectMode();
}

function downloadFile(fileId) {
    return new Promise((resolve) => {
        const link = document.createElement('a');
        link.href = `download-handler.php?id=${fileId}`;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        resolve();
    });
}

// ==================== DELETE ====================

async function deleteSelected() {
    const checkboxes = document.querySelectorAll('.file-card-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showToast('Please select files to delete', 'error');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${checkboxes.length} file(s)? This cannot be undone.`)) {
        return;
    }
    
    let deleted = 0;
    
    for (let checkbox of checkboxes) {
        const success = await deleteFile(checkbox.value);
        if (success) deleted++;
    }
    
    showToast(`${deleted} file(s) deleted`, 'success');
    
    setTimeout(() => {
        window.location.reload();
    }, 1500);
}

async function deleteFile(fileId) {
    try {
        const response = await fetch('delete-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ file_id: fileId })
        });
        
        const data = await response.json();
        return data.success;
        
    } catch (error) {
        console.error('Delete error:', error);
        return false;
    }
}

// ==================== PREVIEW (ENHANCED WITH DOCUMENT VIEWER) ====================

function previewFile(fileId, storedName, originalName, extension) {
    console.log('Preview clicked!', extension); // DEBUG LINE
    alert('File type: ' + extension); // DEBUG LINE
    
    if (selectMode) return;
    
    // ... rest of code

// function previewFile(fileId, storedName, originalName, extension) {
    //if (selectMode) return; // Don't preview in select mode
    
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    const pdfExtensions = ['pdf'];
    const officeExtensions = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    const textExtensions = ['txt', 'csv'];
    
    extension = extension.toLowerCase();
    
    if (imageExtensions.includes(extension)) {
        // Image preview
        showImagePreview(storedName, originalName);
    } else if (pdfExtensions.includes(extension)) {
        // PDF preview
        showPDFPreview(storedName, originalName);
    } else if (officeExtensions.includes(extension)) {
        // Office document preview
        showOfficePreview(storedName, originalName, extension);
    } else if (textExtensions.includes(extension)) {
        // Text file preview
        showTextPreview(fileId, originalName);
    } else {
        // Download if no preview available
        downloadFile(fileId);
    }
}

function showImagePreview(storedName, originalName) {
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <button class="modal-close" onclick="closePreview()">×</button>
        <img src="uploads/${storedName}" alt="${originalName}" class="modal-image">
    `;
    document.getElementById('previewModal').style.display = 'flex';
}

function showPDFPreview(storedName, originalName) {
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <button class="modal-close" onclick="closePreview()">×</button>
        <div style="width: 90vw; height: 90vh; background: white; border-radius: 1rem; overflow: hidden;">
            <iframe 
                src="uploads/${storedName}" 
                style="width: 100%; height: 100%; border: none;"
                type="application/pdf">
            </iframe>
        </div>
    `;
    document.getElementById('previewModal').style.display = 'flex';
}

function showOfficePreview(storedName, originalName, extension) {
    const modalContent = document.getElementById('modalContent');
    
    // Get full URL for the file
    const fileUrl = encodeURIComponent(window.location.origin + '/UploadX/uploads/' + storedName);
    
    // Try Google Docs Viewer first
    const viewerUrl = `https://docs.google.com/gview?url=${fileUrl}&embedded=true`;
    
    modalContent.innerHTML = `
        <button class="modal-close" onclick="closePreview()">×</button>
        <div style="width: 90vw; height: 90vh; background: white; border-radius: 1rem; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                <h3 style="margin: 0; color: #1f2937; font-size: 1.25rem;">📄 ${originalName}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6b7280; font-size: 0.875rem;">
                    Preview may take a moment to load. 
                    <a href="download-handler.php?id=${storedName.split('_')[0]}" style="color: #2563eb; text-decoration: underline;">Download instead</a>
                </p>
            </div>
            <iframe 
                src="${viewerUrl}" 
                style="width: 100%; flex: 1; border: none;"
                frameborder="0">
            </iframe>
        </div>
    `;
    document.getElementById('previewModal').style.display = 'flex';
}

async function showTextPreview(fileId, originalName) {
    try {
        const response = await fetch(`preview-handler.php?id=${fileId}`);
        const text = await response.text();
        
        const modalContent = document.getElementById('modalContent');
        modalContent.innerHTML = `
            <button class="modal-close" onclick="closePreview()">×</button>
            <div style="width: 800px; max-width: 90vw; max-height: 90vh; background: white; border-radius: 1rem; overflow: hidden; display: flex; flex-direction: column;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                    <h3 style="margin: 0; color: #1f2937; font-size: 1.25rem;">📃 ${originalName}</h3>
                </div>
                <div style="padding: 2rem; overflow-y: auto; flex: 1; font-family: 'Courier New', monospace; white-space: pre-wrap; line-height: 1.6; color: #374151;">
                    ${text.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                </div>
            </div>
        `;
        document.getElementById('previewModal').style.display = 'flex';
    } catch (error) {
        showToast('Failed to load text preview', 'error');
        downloadFile(fileId);
    }
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    modal.style.display = 'none';
    document.getElementById('modalContent').innerHTML = '<button class="modal-close" onclick="closePreview()">×</button>';
}

function closePreview() {
    previewModal.style.display = 'none';
    document.getElementById('previewImage').src = '';
}

// Close modal on background click
previewModal.addEventListener('click', (e) => {
    if (e.target === previewModal) {
        closePreview();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && previewModal.style.display === 'flex') {
        closePreview();
    }
});

// ==================== TOAST NOTIFICATIONS ====================

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '⚠️' : 'ℹ️';
    
    toast.innerHTML = `
        <span class="toast-icon">${icon}</span>
        <div class="toast-content">
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// ==================== INITIALIZATION ====================

document.addEventListener('DOMContentLoaded', () => {
    console.log('UploadX File Manager loaded successfully! 🚀');
    
    // Add smooth animations on load
    document.querySelectorAll('.file-card').forEach((card, index) => {
        card.style.animation = `slideUp 0.4s ease-out ${index * 0.05}s backwards`;
    });
});
