<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Flidoh Construction</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Barlow', sans-serif;
            background: #0a0a0a;
            color: #e0e0e0;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #d4af37;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #1a1a1a;
            padding: 25px;
            border: 1px solid #d4af37;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 2.5rem;
            color: #d4af37;
            font-weight: bold;
            display: block;
        }

        .stat-card .label {
            font-size: 0.9rem;
            color: #999;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .messages-table {
            background: #1a1a1a;
            border: 1px solid #d4af37;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-header {
            background: #d4af37;
            color: #000;
            padding: 15px;
            font-weight: bold;
        }

        .table-row {
            border-bottom: 1px solid #333;
            transition: background 0.3s;
        }

        .table-row:hover {
            background: #222;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .table-cell {
            padding: 15px;
            vertical-align: top;
        }

        .message-content {
            max-width: 300px;
            word-wrap: break-word;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-unread {
            background: #4a9a4a;
            color: #fff;
        }

        .status-read {
            background: #9a9a4a;
            color: #fff;
        }

        .status-responded {
            background: #4a4a9a;
            color: #fff;
        }

        .action-btn {
            background: #d4af37;
            color: #000;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-right: 5px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background: #e5c158;
            transform: scale(1.05);
        }

        .refresh-btn {
            background: #d4af37;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .refresh-btn:hover {
            background: #e5c158;
            transform: scale(1.05);
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .error {
            background: #3a1a1a;
            border: 1px solid #9a4a4a;
            color: #ff7e7e;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .message-details {
            background: #0a0a0a;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            border-left: 3px solid #d4af37;
        }

        .message-details h4 {
            color: #d4af37;
            margin-bottom: 10px;
        }

        .message-details p {
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .email-link {
            color: #d4af37;
            text-decoration: none;
        }

        .email-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1><i class="fa-solid fa-cog"></i> Admin Panel</h1>
            <button class="refresh-btn" onclick="loadMessages()">
                <i class="fa-solid fa-rotate-right"></i> Refresh
            </button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <span class="number" id="totalMessages">0</span>
                <div class="label">Total Messages</div>
            </div>
            <div class="stat-card">
                <span class="number" id="unreadMessages">0</span>
                <div class="label">Unread</div>
            </div>
            <div class="stat-card">
                <span class="number" id="todayMessages">0</span>
                <div class="label">Today</div>
            </div>
            <div class="stat-card">
                <span class="number" id="respondedMessages">0</span>
                <div class="label">Responded</div>
            </div>
        </div>

        <div id="messagesTable"></div>
    </div>

    <script>
        async function loadMessages() {
            const tableDiv = document.getElementById('messagesTable');
            tableDiv.innerHTML = '<div class="loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading messages...</div>';

            try {
                const response = await fetch('get_messages.php');
                const data = await response.json();

                if (data.success) {
                    displayMessages(data.messages);
                    updateStats(data.messages);
                } else {
                    tableDiv.innerHTML = '<div class="error">Error: ' + data.message + '</div>';
                }
            } catch (error) {
                tableDiv.innerHTML = '<div class="error">Error loading messages: ' + error.message + '</div>';
            }
        }

        function updateStats(messages) {
            const total = messages.length;
            const unread = messages.filter(m => m.status === 'unread').length;
            const responded = messages.filter(m => m.status === 'responded').length;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const todayCount = messages.filter(m => {
                const msgDate = new Date(m.timestamp);
                msgDate.setHours(0, 0, 0, 0);
                return msgDate.getTime() === today.getTime();
            }).length;

            document.getElementById('totalMessages').textContent = total;
            document.getElementById('unreadMessages').textContent = unread;
            document.getElementById('todayMessages').textContent = todayCount;
            document.getElementById('respondedMessages').textContent = responded;
        }

        function displayMessages(messages) {
            if (messages.length === 0) {
                document.getElementById('messagesTable').innerHTML =
                    '<div class="loading">No messages yet.</div>';
                return;
            }

            let html = `
                <div class="messages-table">
                    <div class="table-header">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 15%;">Name</td>
                                <td style="width: 20%;">Contact</td>
                                <td style="width: 15%;">Service</td>
                                <td style="width: 25%;">Message</td>
                                <td style="width: 10%;">Status</td>
                                <td style="width: 15%;">Actions</td>
                            </tr>
                        </table>
                    </div>
            `;

            messages.forEach(msg => {
                const statusClass = 'status-' + msg.status;
                const truncatedMessage = msg.message.length > 100
                    ? msg.message.substring(0, 100) + '...'
                    : msg.message;

                html += `
                    <div class="table-row">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td class="table-cell" style="width: 15%;">
                                    <strong>${msg.firstName} ${msg.lastName}</strong>
                                </td>
                                <td class="table-cell" style="width: 20%;">
                                    <a href="mailto:${msg.email}" class="email-link">${msg.email}</a><br>
                                    <small>${new Date(msg.timestamp).toLocaleString()}</small>
                                </td>
                                <td class="table-cell" style="width: 15%;">
                                    ${msg.service || 'Not specified'}
                                </td>
                                <td class="table-cell message-content" style="width: 25%;">
                                    ${truncatedMessage}
                                    ${msg.message.length > 100 ? '<br><button onclick="showFullMessage(' + msg.id + ')" style="margin-top: 5px; font-size: 0.8rem;">Read more</button>' : ''}
                                </td>
                                <td class="table-cell" style="width: 10%;">
                                    <span class="status-badge ${statusClass}">${msg.status}</span>
                                </td>
                                <td class="table-cell" style="width: 15%;">
                                    <button class="action-btn" onclick="markAsRead(${msg.id})">
                                        <i class="fa-solid fa-check"></i> Read
                                    </button>
                                    <button class="action-btn" onclick="markAsResponded(${msg.id})">
                                        <i class="fa-solid fa-reply"></i> Done
                                    </button>
                                </td>
                            </tr>
                        </table>
                        <div id="details-${msg.id}" class="message-details" style="display: none;">
                            <h4>Full Message:</h4>
                            <p>${msg.message}</p>
                            <p><strong>IP Address:</strong> ${msg.ip}</p>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            document.getElementById('messagesTable').innerHTML = html;
        }

        function showFullMessage(id) {
            const detailsDiv = document.getElementById('details-' + id);
            detailsDiv.style.display = detailsDiv.style.display === 'none' ? 'block' : 'none';
        }

        function markAsRead(id) {
            updateMessageStatus(id, 'read');
        }

        function markAsResponded(id) {
            updateMessageStatus(id, 'responded');
        }

        async function updateMessageStatus(id, status) {
            try {
                const response = await fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id, status: status })
                });

                const data = await response.json();

                if (data.success) {
                    // Reload messages to show updated status
                    loadMessages();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            } catch (error) {
                alert('Error updating status: ' + error.message);
            }
        }

        // Load messages on page load
        loadMessages();

        // Auto-refresh every 60 seconds
        setInterval(loadMessages, 60000);
    </script>
</body>
</html>
