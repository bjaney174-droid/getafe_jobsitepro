/**
 * Application Notifications System
 * Real-time notifications and message polling
 */

class ApplicationNotifications {
    constructor(appId, userId, baseUrl = '/JobsitePro/public/') {
        this.appId = appId;
        this.userId = userId;
        this.baseUrl = baseUrl;
        this.pollingInterval = 30000; // 30 seconds
        this.lastMessageCount = 0;
        this.isPolling = false;

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        this.init();
    }

    init() {
        // Start polling for new messages
        this.startPolling();

        // Update UI on page visibility
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.checkForNewMessages();
            }
        });
    }

    startPolling() {
        if (this.isPolling) return;

        this.isPolling = true;
        this.pollingTimer = setInterval(() => {
            this.checkForNewMessages();
        }, this.pollingInterval);

        // Initial check
        this.checkForNewMessages();
    }

    stopPolling() {
        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
            this.isPolling = false;
        }
    }

    async checkForNewMessages() {
        try {
            const response = await fetch(
                `${this.baseUrl}api/get-application-messages.php?app_id=${this.appId}`
            );
            const data = await response.json();

            if (data.success) {
                const unreadCount = data.unread_count || 0;

                // Update badge
                this.updateBadge(unreadCount);

                // Show notification if new messages
                if (unreadCount > this.lastMessageCount) {
                    const newMessages = unreadCount - this.lastMessageCount;
                    this.showNotification(`You have ${newMessages} new message(s)`);
                }

                this.lastMessageCount = unreadCount;

                // Mark messages as read if viewing
                if (!document.hidden && unreadCount > 0) {
                    this.markMessagesAsRead();
                }
            }
        } catch (error) {
            console.error('Error checking messages:', error);
        }
    }

    updateBadge(count) {
        let badge = document.getElementById('app_unread_badge');

        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'app_unread_badge';
                badge.style.cssText = `
                    display: inline-block;
                    background: #dc3545;
                    color: white;
                    border-radius: 50%;
                    padding: 5px 10px;
                    font-weight: bold;
                    font-size: 12px;
                    margin-left: 10px;
                `;

                const heading = document.querySelector('h1, h2, h3');
                if (heading) {
                    heading.appendChild(badge);
                }
            }
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else if (badge) {
            badge.style.display = 'none';
        }
    }

    async markMessagesAsRead() {
        const csrfToken = document.querySelector('[name="csrf_token"]')?.value;
        if (!csrfToken) return;

        const formData = new FormData();
        formData.append('action', 'mark_read');
        formData.append('app_id', this.appId);
        formData.append('csrf_token', csrfToken);

        try {
            await fetch(`${this.baseUrl}api/handle-application-message.php`, {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    showNotification(message) {
        // Browser notification
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification('Getafe Jobsite', {
                body: message,
                icon: `${this.baseUrl}../assets/icon.png`,
                tag: 'app-notification'
            });
        }

        // In-app notification
        this.showInAppNotification(message);
    }

    showInAppNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 9999;
            animation: slideIn 0.3s ease-in-out;
        `;
        notification.textContent = message;

        // Add animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        if (!document.querySelector('style[data-notification]')) {
            style.setAttribute('data-notification', 'true');
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in-out';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Send message with file support
    async sendMessage(message, attachmentFile = null) {
        const csrfToken = document.querySelector('[name="csrf_token"]')?.value;
        if (!csrfToken) {
            alert('Security token missing');
            return false;
        }

        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('app_id', this.appId);
        formData.append('message', message);
        formData.append('csrf_token', csrfToken);

        if (attachmentFile) {
            formData.append('file', attachmentFile);
        }

        try {
            const response = await fetch(`${this.baseUrl}api/handle-application-message.php`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification('Message sent successfully');
                // Reload messages
                this.checkForNewMessages();
                return true;
            } else {
                alert(data.message || 'Error sending message');
                return false;
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message');
            return false;
        }
    }

    // Schedule interview
    async scheduleInterview(interviewDate, interviewLocation, message = '') {
        const csrfToken = document.querySelector('[name="csrf_token"]')?.value;
        if (!csrfToken) {
            alert('Security token missing');
            return false;
        }

        const formData = new FormData();
        formData.append('action', 'schedule_interview');
        formData.append('app_id', this.appId);
        formData.append('interview_date', interviewDate);
        formData.append('interview_location', interviewLocation);
        formData.append('interview_message', message);
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch(`${this.baseUrl}api/handle-application-message.php`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification('Interview scheduled and email sent');
                return true;
            } else {
                alert(data.message || 'Error scheduling interview');
                return false;
            }
        } catch (error) {
            console.error('Error scheduling interview:', error);
            alert('Error scheduling interview');
            return false;
        }
    }

    // Update application status
    async updateStatus(status, notes = '') {
        const csrfToken = document.querySelector('[name="csrf_token"]')?.value;
        if (!csrfToken) {
            alert('Security token missing');
            return false;
        }

        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('app_id', this.appId);
        formData.append('status', status);
        formData.append('notes', notes);
        formData.append('csrf_token', csrfToken);

        try {
            const response = await fetch(`${this.baseUrl}api/handle-application-message.php`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                this.showNotification('Status updated and email sent');
                return true;
            } else {
                alert(data.message || 'Error updating status');
                return false;
            }
        } catch (error) {
            console.error('Error updating status:', error);
            alert('Error updating status');
            return false;
        }
    }
}

// Initialize on page load if application is being viewed
document.addEventListener('DOMContentLoaded', function() {
    const appIdElement = document.querySelector('[data-app-id]');
    const userIdElement = document.querySelector('[data-user-id]');

    if (appIdElement && userIdElement) {
        const appNotifications = new ApplicationNotifications(
            appIdElement.dataset.appId,
            userIdElement.dataset.userId
        );

        // Make it global for use in other scripts
        window.appNotifications = appNotifications;
    }
});
