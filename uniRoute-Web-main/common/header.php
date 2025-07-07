<style>
    
    .header {
            background: linear-gradient(to right, #007bff, #28a745);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .header nav {
            display: flex;
            gap: 20px;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .header nav a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header .user-info span {
            font-size: 16px;
        }

        .header .logout-btn {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .header .logout-btn:hover {
            background-color: #c9302c;
        }

        .hamburger {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

</style>


<header class="header">
    <div class="logo">UniRoute</div>
    <nav id="nav-menu">
        <a href="../home/home.php">Home</a>
        <a href="../Contact_us/contact.php">Contact Us</a>
        <a href="../feedback/feedback.php">Feedback</a>
        <a href="../bus_tracking/index.php">Map</a>
        <a href="../user/viewProfile.php">My Profile</a>

    </nav>
    <div class="user-info">
        <span>Welcome, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
    </div>
    <button class="hamburger" onclick="toggleMenu()">☰</button>
</header>

<script>
function toggleMenu() {
    document.getElementById('nav-menu').classList.toggle('active');
}
</script>
