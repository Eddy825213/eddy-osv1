(function () {
    // Utility to escape HTML to prevent XSS
    function escapeHTML(str) {
        return str.replace(/[&<>"']/g, match => ({
            '&': '&',
            '<': '<',
            '>': '>',
            '"': '"',
            "'": '',
        })[match]);
    }

    // Show debug message on page
    function showDebugMessage(message, isError = false) {
        const debugDiv = document.createElement('div');
        debugDiv.style.position = 'fixed';
        debugDiv.style.top = '10px';
        debugDiv.style.left = '10px';
        debugDiv.style.background = isError ? '#ff5555' : '#00cc00';
        debugDiv.style.color = '#fff';
        debugDiv.style.padding = '10px';
        debugDiv.style.zIndex = '10000';
        debugDiv.textContent = message;
        document.body.appendChild(debugDiv);
        setTimeout(() => debugDiv.remove(), 5000);
    }

    // Show notification
    function showNotification(message, isError = false) {
        const existingModal = document.getElementById('comment-notification');
        if (existingModal) existingModal.remove();

        const modal = document.createElement('div');
        modal.id = 'comment-notification';
        modal.className = 'devtools-warning';
        modal.innerHTML = `
            <div class="devtools-warning-content">
                <h2>${isError ? '🚫 Error!' : '🌸 Success!'}</h2>
                <p>${message}</p>
                <button class="btn close-notification">Got it!</button>
            </div>
        `;
        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('.close-notification');
        closeBtn.addEventListener('click', () => {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        });

        setTimeout(() => {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        }, 3000);
    }

    // Load comments từ server (cho người dùng thường)
    function loadComments() {
        fetch('/comments.php')
            .then(response => {
                if (!response.ok) throw new Error('Failed to load comments: ' + response.status);
                return response.json();
            })
            .then(data => {
                const commentList = document.getElementById('comment-list');
                commentList.innerHTML = '';
                (data.comments || []).sort((a, b) => b.timestamp - a.timestamp).forEach(comment => {
                    const commentItem = document.createElement('div');
                    commentItem.classList.add('comment-item');
                    commentItem.innerHTML = `
                        <p class="comment-author">${escapeHTML(comment.name)}</p>
                        <p class="comment-content">${escapeHTML(comment.content)}</p>
                        <p class="comment-time">${new Date(comment.timestamp).toLocaleString('en-GB')}</p>
                    `;
                    commentList.appendChild(commentItem);
                });
                showDebugMessage('Comments loaded successfully');
            })
            .catch(error => {
                showDebugMessage('Error loading comments: ' + error.message, true);
            });
    }

    // Load comments cho admin
    function loadAdminComments(adminCode) {
        fetch(`/comments.php?admin=${adminCode}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load admin comments: ' + response.status);
                return response.json();
            })
            .then(data => {
                const adminCommentList = document.getElementById('admin-comment-list');
                adminCommentList.innerHTML = '';
                (data.comments || []).sort((a, b) => b.timestamp - a.timestamp).forEach(comment => {
                    const commentItem = document.createElement('div');
                    commentItem.classList.add('comment-item');
                    commentItem.style.border = '1px solid #ff69b4';
                    commentItem.style.marginBottom = '10px';
                    commentItem.innerHTML = `
                        <p class="comment-author">${escapeHTML(comment.name)}</p>
                        <p class="comment-content">${escapeHTML(comment.content)}</p>
                        <p class="comment-time">${new Date(comment.timestamp).toLocaleString('en-GB')}</p>
                        <button class="btn delete-btn" data-id="${comment.id}" style="background: #ff5555;">Delete</button>
                    `;
                    adminCommentList.appendChild(commentItem);
                });

                // Gắn sự kiện cho nút Delete
                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const commentId = btn.getAttribute('data-id');
                        fetch('/comments.php', {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: commentId, adminCode: adminCode })
                        })
                            .then(response => {
                                if (!response.ok) throw new Error('Failed to delete comment');
                                return response.json();
                            })
                            .then(data => {
                                showNotification(data.message);
                                loadAdminComments(adminCode);
                                loadComments();
                            })
                            .catch(error => {
                                showNotification('Error deleting comment: ' + error.message, true);
                            });
                    });
                });

                showDebugMessage('Admin comments loaded successfully');
            })
            .catch(error => {
                showNotification('Error loading admin comments: ' + error.message, true);
            });
    }

    // Initialize comment section
    document.addEventListener('DOMContentLoaded', () => {
        loadComments();

        const nameInput = document.getElementById('comment-name');
        const contentInput = document.getElementById('comment-content');
        const submitBtn = document.getElementById('submit-comment');
        const adminPasswordInput = document.getElementById('admin-password');
        const adminLoginBtn = document.getElementById('admin-login-btn');
        const adminLogoutBtn = document.getElementById('admin-logout-btn');
        const adminControls = document.getElementById('admin-controls');
        let lastCommentTime = 0;
        let isAdminLoggedIn = false;
        let adminCode = '';

        if (!submitBtn || !nameInput || !contentInput || !adminPasswordInput || !adminLoginBtn || !adminControls) {
            showDebugMessage('One or more comment/admin form elements are missing!', true);
            return;
        }

        // Xử lý đăng nhập admin
        adminLoginBtn.addEventListener('click', () => {
            const code = adminPasswordInput.value.trim();
            if (!code) {
                showNotification('Please enter admin code!', true);
                return;
            }

            // Thử tải danh sách bình luận admin để xác thực
            fetch(`/comments.php?admin=${code}`)
                .then(response => {
                    if (!response.ok) throw new Error('Invalid admin code');
                    return response.json();
                })
                .then(data => {
                    isAdminLoggedIn = true;
                    adminCode = code;
                    adminControls.style.display = 'block';
                    adminPasswordInput.value = '';
                    loadAdminComments(adminCode);
                    showNotification('Admin login successful! Admin Code: ' + data.adminCode);
                })
                .catch(error => {
                    showNotification('Error logging in: ' + error.message, true);
                });
        });

        // Xử lý đăng xuất admin
        adminLogoutBtn.addEventListener('click', () => {
            isAdminLoggedIn = false;
            adminCode = '';
            adminControls.style.display = 'none';
            document.getElementById('admin-comment-list').innerHTML = '';
            showNotification('Admin logged out!');
        });

        // Xử lý gửi bình luận
        submitBtn.addEventListener('click', () => {
            showDebugMessage('Submit button clicked');
            const now = Date.now();
            const name = nameInput.value.trim();
            const content = contentInput.value.trim();

            showDebugMessage('Name: ' + name + ', Content: ' + content);

            // Validation
            if (!content) {
                showNotification('Comment cannot be empty!', true);
                return;
            }
            if (content.length > 500) {
                showNotification('Comment is too long (max 500 characters)!', true);
                return;
            }
            if (name.length > 50) {
                showNotification('Name is too long (max 50 characters)!', true);
                return;
            }
            if (now - lastCommentTime < 30000) {
                showNotification('Please wait 30 seconds before posting again!', true);
                return;
            }

            // Gửi bình luận
            showDebugMessage('Sending fetch request to /comments.php');
            fetch('/comments.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, content })
            })
                .then(response => {
                    showDebugMessage('Fetch response status: ' + response.status);
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Failed to post comment');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    showDebugMessage('Fetch response data: ' + JSON.stringify(data));
                    if (data.success) {
                        loadComments();
                        if (isAdminLoggedIn) loadAdminComments(adminCode);
                        nameInput.value = '';
                        contentInput.value = '';
                        lastCommentTime = now;
                        showNotification(data.message);
                    } else {
                        showNotification(data.error || 'Failed to post comment!', true);
                    }
                })
                .catch(error => {
                    showDebugMessage('Error posting comment: ' + error.message, true);
                });
        });

        contentInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                submitBtn.click();
            }
        });
    });
})();