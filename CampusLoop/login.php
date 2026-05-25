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
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user["password"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CampusLoop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                <div class="theme-toggle-container"></div>
            </div>
            <h2>Welcome back</h2>
            <p>Log in to continue</p>
            
            <?php if ($message): ?>
                <div style="background: rgba(239, 68, 68, 0.1); padding: 0.8rem; border-radius: 0.75rem; margin-bottom: 1rem; text-align: center; font-size: 0.8rem; color: var(--error);">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
                
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
                
                <button type="submit" class="btn btn-primary">Sign In →</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem;">
                Don't have an account? <a href="register.php" style="color: var(--accent); text-decoration: none;">Sign up</a>
            </p>
        </div>
    </div>
    
    <script src="theme.js"></script>
    <footer class="footer">
    <p>© 2026 CampusLoop — Made for students, by students</p>
    <p style="margin-top: 0.5rem;">
        <a href="terms.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Terms</a>
        <a href="support.php" style="color: var(--text-muted); text-decoration: none; margin: 0 0.5rem;">Support</a>
    </p>
</footer>
</body>
</html>