<?php
session_start();
include("config.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle sending a new message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $receiver_id = (int)$_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $receiver_id, $message);
        $stmt->execute();
        header("Location: messages.php?chat=" . $receiver_id);
        exit;
    }
}

// Handle starting a new conversation
$start_user_id = isset($_GET['start']) ? (int)$_GET['start'] : 0;
if ($start_user_id > 0 && $start_user_id != $user_id) {
    header("Location: messages.php?chat=" . $start_user_id);
    exit;
}

// Get all users for search (excluding self)
$all_users = $conn->query("SELECT id, username FROM users WHERE id != $user_id ORDER BY username");

// Get conversations
$conversations = $conn->query("
    SELECT DISTINCT 
        u.id, u.username,
        (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = $user_id) OR (sender_id = $user_id AND receiver_id = u.id) ORDER BY id DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id = $user_id) OR (sender_id = $user_id AND receiver_id = u.id) ORDER BY id DESC LIMIT 1) as last_time,
        (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = $user_id AND is_read = 0) as unread_count
    FROM users u
    WHERE u.id IN (SELECT sender_id FROM messages WHERE receiver_id = $user_id)
       OR u.id IN (SELECT receiver_id FROM messages WHERE sender_id = $user_id)
    ORDER BY last_time DESC
");

// Get selected chat
$selected_chat = isset($_GET['chat']) ? (int)$_GET['chat'] : 0;
$chat_user = null;
$messages = [];

if ($selected_chat > 0) {
    $chat_user = $conn->query("SELECT id, username FROM users WHERE id = $selected_chat")->fetch_assoc();
    if ($chat_user) {
        // Mark messages as read
        $conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = $selected_chat AND receiver_id = $user_id");
        
        // Get messages
        $msg_query = $conn->query("
            SELECT * FROM messages 
            WHERE (sender_id = $user_id AND receiver_id = $selected_chat) 
               OR (sender_id = $selected_chat AND receiver_id = $user_id)
            ORDER BY created_at ASC
        ");
        while ($row = $msg_query->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-container {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .search-input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid var(--border);
            border-radius: 40px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 0.85rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--accent-gold);
        }
        
        .user-results {
            max-height: 200px;
            overflow-y: auto;
            border-top: 1px solid var(--border);
        }
        
        .user-result-item {
            padding: 0.8rem 1rem;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid var(--border-light);
        }
        
        .user-result-item:hover {
            background: var(--accent-gold-glow);
        }
        
        .start-conversation-btn {
            width: 100%;
            padding: 0.7rem;
            background: var(--accent-dark);
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }
        
        .start-conversation-btn:hover {
            background: var(--accent-gold);
            color: #111;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="index.php" class="logo">
                <div class="logo-icon">CL</div>
                <span class="logo-text">CampusLoop</span>
            </a>
            <div class="nav-links">
                <a href="index.php">Marketplace</a>
                <a href="add_listing.php">Sell</a>
                <a href="my_listings.php">My Items</a>
                <a href="messages.php" style="color: var(--accent-gold);">Messages</a>
                <a href="order_history.php">Orders</a>
            </div>
            <div class="nav-actions">
                <div class="theme-toggle-container"></div>
                <a href="cart.php" class="btn btn-outline" style="padding: 0.4rem 1rem;">
                    🛒 Cart <span id="cartCount">0</span>
                </a>
                <span style="color: var(--text-secondary);">👋 <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container page-content">
        <h1 style="margin: 1.5rem 0;">Messages</h1>
        
        <div class="messages-layout">
            <!-- Conversations Sidebar -->
            <div class="conversation-list">
                <div class="search-container">
                    <button class="start-conversation-btn" id="newChatBtn">+ New Conversation</button>
                    <div id="searchSection" style="display: none; margin-top: 0.5rem;">
                        <input type="text" id="searchUser" class="search-input" placeholder="Search for a student..." autocomplete="off">
                        <div id="searchResults" class="user-results"></div>
                    </div>
                </div>
                
                <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                    <strong>Conversations</strong>
                </div>
                
                <div id="conversationList">
                    <?php if ($conversations->num_rows > 0): ?>
                        <?php while($conv = $conversations->fetch_assoc()): ?>
                            <a href="?chat=<?php echo $conv['id']; ?>" class="conversation-item <?php echo ($selected_chat == $conv['id']) ? 'active' : ''; ?>">
                                <div style="font-weight: 500;"><?php echo htmlspecialchars($conv['username']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars(substr($conv['last_message'] ?? 'No messages', 0, 35)); ?></div>
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span style="background: var(--accent-gold); color: #111; border-radius: 10px; padding: 0.1rem 0.5rem; font-size: 0.65rem; display: inline-block; margin-top: 0.25rem;"><?php echo $conv['unread_count']; ?> new</span>
                                <?php endif; ?>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            <span style="font-size: 2rem;">💬</span>
                            <p>No conversations yet</p>
                            <p style="font-size: 0.8rem;">Click "New Conversation" to message someone</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="chat-area">
                <?php if ($selected_chat > 0 && $chat_user): ?>
                    <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                        <strong>💬 <?php echo htmlspecialchars($chat_user['username']); ?></strong>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message <?php echo ($msg['sender_id'] == $user_id) ? 'message-sent' : 'message-received'; ?>">
                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                <div style="font-size: 0.6rem; margin-top: 0.2rem; opacity: 0.7;">
                                    <?php echo date('M d, H:i', strtotime($msg['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form method="POST" class="chat-input">
                        <input type="hidden" name="receiver_id" value="<?php echo $selected_chat; ?>">
                        <input type="text" name="message" placeholder="Type your message..." autocomplete="off" required>
                        <button type="submit" name="send_message" class="btn btn-primary">Send →</button>
                    </form>
                <?php else: ?>
                    <div style="flex: 1; display: flex; align-items: center; justify-content: center; flex-direction: column; color: var(--text-muted);">
                        <span style="font-size: 3rem;">💬</span>
                        <p>Select a conversation to start chatting</p>
                        <p style="font-size: 0.8rem;">Or click "New Conversation" to message a student</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© 2026 CampusLoop — Made for students, by students</p>
    </footer>

    <script src="theme.js"></script>
    <script src="animations.js"></script>
    <script>
        // New Chat Button Toggle
        const newChatBtn = document.getElementById('newChatBtn');
        const searchSection = document.getElementById('searchSection');
        const searchInput = document.getElementById('searchUser');
        const searchResults = document.getElementById('searchResults');
        
        if (newChatBtn) {
            newChatBtn.addEventListener('click', () => {
                if (searchSection.style.display === 'none') {
                    searchSection.style.display = 'block';
                    searchInput?.focus();
                } else {
                    searchSection.style.display = 'none';
                    searchResults.innerHTML = '';
                }
            });
        }
        
        // Search Users
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }
                
                searchTimeout = setTimeout(() => {
                    fetch(`ajax_search_users.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(users => {
                            if (users.length === 0) {
                                searchResults.innerHTML = '<div style="padding: 1rem; color: var(--text-muted);">No users found</div>';
                            } else {
                                searchResults.innerHTML = users.map(user => `
                                    <div class="user-result-item" onclick="startNewChat(${user.id})">
                                        👤 ${escapeHtml(user.username)}
                                    </div>
                                `).join('');
                            }
                        });
                }, 300);
            });
        }
        
        function startNewChat(userId) {
            window.location.href = `messages.php?chat=${userId}`;
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Scroll chat to bottom
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            let count = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
            let span = document.getElementById('cartCount');
            if (span) span.textContent = count;
        }
        updateCartCount();
    </script>

    <footer class="footer">
    <p>© 2026 CampusLoop — Made for students, by students</p>
    <p style="margin-top: 0.5rem;">
        <a href="terms.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Terms</a>
        <a href="support.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Support</a>
    </p>
</footer>

</body>
</html>