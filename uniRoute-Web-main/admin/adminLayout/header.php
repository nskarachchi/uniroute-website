<style>
    /* Base Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Header Styles */
.main-header {
    background: linear-gradient(to right, #007bff, #28a745);
    color: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.main-header .logo {
    font-size: 24px;
    font-weight: bold;
    flex: 1 1 auto;
}

/* Navigation */
.main-header nav {
    display: flex;
    gap: 20px;
    align-items: center;
}

.main-header nav a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.main-header nav a:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.logout-btn {
    background-color: #d9534f;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #c9302c;
}

/* Hamburger Button */
.hamburger {
    display: none;
    font-size: 24px;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
}

/* Responsive Navigation */
@media (max-width: 768px) {
    .main-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .main-header nav {
        display: none;
        flex-direction: column;
        width: 100%;
        margin-top: 10px;
    }

    .main-header nav.active {
        display: flex;
    }

    .logout-btn {
        width: 100%;
        text-align: center;
    }

    .hamburger {
        display: block;
        align-self: flex-end;
    }
}

</style>
<header class="main-header">
    <div class="logo">UniRoute Admin</div>
    
    <nav id="nav-menu">
        <a href="../admin/dashboard.php">home</a>
        <a href="../admin/admin.php">Shuttles</a>
        <a href="../admin/adminUsers.php">Users</a>
        <a href="../admin/bookings.php">Bookings</a>
        <a href="../admin/routes.php">Routes</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>

    <button class="hamburger" onclick="toggleMenu()">☰</button>
</header>
<script>
function toggleMenu() {
    document.getElementById('nav-menu').classList.toggle('active');
}
</script>

