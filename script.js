const container = document.getElementById('container');
const signUpButton = document.getElementById('register');
const signInButton = document.getElementById('login');

// Ketika tombol 'Daftar' diklik, pindahkan form ke halaman registrasi
signUpButton.addEventListener('click', () => {
    container.classList.add('active'); // Menambahkan class 'active' untuk memindahkan form
});

// Ketika tombol 'Masuk' diklik, pindahkan form ke halaman login
signInButton.addEventListener('click', () => {
    container.classList.remove('active'); // Menghapus class 'active' untuk kembali ke form login
});
