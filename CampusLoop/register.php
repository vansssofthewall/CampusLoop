<?php
session_start();
include("config.php");

if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    
    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif (strlen($username) < 3) {
        $message = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 4) {
        $message = "Password must be at least 4 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $message = "Username already taken.";
        } else {
            // Remove email from the insert - just username and password
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION["user_id"] = $conn->insert_id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = "user";
                header("Location: index.php");
                exit;
            } else {
                $message = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Create account</h2>
            <p>Join the campus marketplace</p>
            
            <?php if ($message): ?>
                <div style="background: var(--accent-gold-glow); color: var(--accent-gold-dark); padding: 0.8rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>
                
                <label>Password</label>
                <input type="password" name="password" placeholder="Choose a password" required>
                
                <button type="submit" class="btn btn-primary">Create Account →</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                Already have an account? <a href="login.php" style="color: var(--accent-gold); text-decoration: none;">Sign in</a>
            </p>
        </div>
    </div>
    
    <footer class="footer">
    <p>© 2026 CampusLoop — Made for students, by students</p>
    <p style="margin-top: 0.5rem;">
        <a href="terms.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Terms</a>
        <a href="support.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Support</a>
    </p>
</footer>

    <script src="theme.js"></script>

</body>
</html>