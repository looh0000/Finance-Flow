<style>
    .admin-navbar {
        background: #013574;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        font-family: Arial;
    }

    .admin-navbar a {
        color: #fff;
        text-decoration: none;
        margin-right: 20px;
        font-weight: bold;
    }

    .admin-navbar a:hover {
        opacity: 0.8;
    }

    .admin-nav-links {
        display: flex;
        gap: 20px;
    }
</style>

<div class="admin-navbar">
    <div class="logo">
        <strong>FinanceFlow • Admin</strong>
    </div>

    <div class="admin-nav-links">
        <a href="/newFinanceFlowapp/views/dashboard.php">Dashboard</a>
        <a href="lista_usuarios.php">Usuários</a>
    </div>
</div>
