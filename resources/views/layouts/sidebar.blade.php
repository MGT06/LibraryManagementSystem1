<div class="sidebar">
    <div class="logo">
        <img src="{{ asset('asset/open-book.png') }}" alt="Logo Perpustakaan">
        <h2>Perpustakaan</h2>
    </div>
    <nav>
        <a href="{{ route('beranda') }}" class="{{ request()->route()->getName() == 'beranda' ? 'active' : '' }}">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="{{ route('transaksi') }}" class="{{ request()->route()->getName() == 'transaksi' ? 'active' : '' }}">
            <i class="fas fa-exchange-alt"></i> Transaksi
        </a>
        <a href="{{ route('peminjam') }}" class="{{ request()->route()->getName() == 'peminjam' ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Peminjam
        </a>
        <a href="{{ route('buku') }}" class="{{ request()->route()->getName() == 'buku' ? 'active' : '' }}">
            <i class="fas fa-book"></i> Data Buku
        </a>
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </nav>
</div>

<style>
.sidebar {
    width: 250px;
    height: 100vh;
    background-color: #2c3e50;
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    padding: 20px 0;
}

.logo {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid #34495e;
}

.logo img {
    width: 80px;
    height: 80px;
    margin-bottom: 10px;
}

.logo h2 {
    margin: 0;
    font-size: 1.5em;
}

nav {
    padding: 20px;
}

nav a {
    display: flex;
    align-items: center;
    color: white;
    text-decoration: none;
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

nav a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

nav a:hover {
    background-color: #34495e;
}

nav a.active {
    background-color: #3498db;
}

.logout-form {
    margin-top: 20px;
}

.logout-button {
    width: 100%;
    display: flex;
    align-items: center;
    background: none;
    border: none;
    color: white;
    padding: 12px 15px;
    cursor: pointer;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.logout-button i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.logout-button:hover {
    background-color: #e74c3c;
}
</style>

