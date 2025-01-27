<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tengko Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style type="text/tailwindcss">
      /* Container */
      .container {
        @apply max-w-7xl mx-auto px-6;
      }

      /* Blue Theme */
      .text-primary {
        @apply text-indigo-700;
      }

      .bg-primary {
        @apply bg-indigo-700;
      }

      .hover-text-primary {
        @apply hover:text-indigo-700;
      }

      .bg-secondary {
        @apply bg-indigo-200;
      }

      /* Hover Animation */
      .hover-scale:hover {
        @apply transform scale-105;
      }

      /* Fade-In Animation */
      .fade-in {
        animation: fadeIn 1.5s ease-in-out forwards;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
        }
        to {
          opacity: 1;
        }
      }

      /* Line Drawing Animation */
      @keyframes draw-line {
        from {
          width: 0;
        }
        to {
          width: 100%;
        }
      }

      /* Morphing Effect */
      @keyframes morph {
        0% {
          border-radius: 10%;
        }
        50% {
          border-radius: 50%;
        }
        100% {
          border-radius: 10%;
        }
      }

      /* Loading Animation */
      @keyframes loading {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      /* Neon Text */
      .neon-text {
        text-shadow: 0 0 10px rgba(0, 255, 255, 1), 0 0 20px rgba(0, 255, 255, 1), 0 0 30px rgba(0, 255, 255, 1);
        animation: neon 1.5s ease-in-out infinite alternate;
      }

      @keyframes neon {
        0% {
          text-shadow: 0 0 10px rgba(0, 255, 255, 1), 0 0 20px rgba(0, 255, 255, 1), 0 0 30px rgba(0, 255, 255, 1);
        }
        50% {
          text-shadow: 0 0 20px rgba(255, 0, 0, 1), 0 0 30px rgba(255, 0, 0, 1), 0 0 40px rgba(255, 0, 0, 1);
        }
        100% {
          text-shadow: 0 0 10px rgba(0, 255, 255, 1), 0 0 20px rgba(0, 255, 255, 1), 0 0 30px rgba(0, 255, 255, 1);
        }
      }

      /* Responsive Typography */
      @media (max-width: 768px) {
        .text-hero {
          @apply text-4xl;
        }
        .text-subheading {
          @apply text-xl;
        }
      }

      /* Buttons */
      .btn {
        @apply text-white font-semibold py-2 px-6 rounded-full shadow-lg transition duration-200 ease-in-out;
      }

      .btn-primary {
        @apply bg-indigo-700 hover:bg-indigo-600;
      }

      .btn-secondary {
        @apply bg-green-600 hover:bg-green-500;
      }

      /* Footer Styling */
      footer {
        @apply bg-indigo-800 text-white py-12;
      }

      footer a {
        @apply text-white hover:text-gray-300 transition duration-200;
      }

    </style>
  </head>
  <body class="bg-gray-50 font-sans leading-relaxed">

    <!-- Navigation (Sticky) -->
    <nav class="bg-white shadow-md py-4 sticky top-0 z-50 fade-in">
      <div class="container flex items-center justify-between">
        <a href="index.php" class="flex items-center space-x-2">
          <span class="text-xl font-bold text-primary">Tengko Laundry</span>
        </a>

        <div class="hidden md:flex space-x-6 font-semibold text-gray-700">
        </div>
        <a href="login.php" class="btn btn-secondary hidden md:block">Masuk</a>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="container md:flex items-center my-12 py-10 bg-gradient-to-r from-indigo-100 to-indigo-50 rounded-lg shadow-md fade-in">
      <div class="md:w-1/2 text-center md:text-left p-6">
        <h4 class="text-2xl text-primary font-semibold mb-2 animate-pulse">Layanan Laundry Profesional</h4>
        <h3 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800 leading-snug">Rasakan Kualitas Terbaik dari Tengko Laundry</h3>
        <p class="text-gray-600 mb-5">Kami memberikan layanan laundry cepat, bersih, dan hemat untuk memudahkan keseharian Anda.</p>
        <a href="login.php" class="btn btn-primary">Mulai Sekarang</a>
      </div>
      <img class="md:w-1/2 rounded-md shadow-lg hover-scale transition duration-200" src="laundry.webp" alt="Layanan Tengko Laundry" />
    </section>

    <!-- Mengapa Memilih Tengko Laundry -->
    <section class="container my-12 text-center p-6 fade-in">
      <h4 class="text-2xl font-semibold text-primary mb-4 animate-pulse">Mengapa Memilih Tengko Laundry?</h4>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-8">
        <div class="bg-secondary p-6 rounded-lg shadow-lg hover-scale transition duration-200">
          <h5 class="text-xl font-semibold text-gray-800">Layanan Cepat</h5>
          <p class="text-gray-600 mt-2">Kami selalu berusaha memberikan layanan laundry yang cepat dan efisien, sehingga Anda tidak perlu khawatir tentang waktu.</p>
        </div>
        <div class="bg-secondary p-6 rounded-lg shadow-lg hover-scale transition duration-200">
          <h5 class="text-xl font-semibold text-gray-800">Kualitas Terjamin</h5>
          <p class="text-gray-600 mt-2">Kualitas laundry kami sudah terjamin. Pakaian Anda akan bersih dan wangi, siap digunakan kembali.</p>
        </div>
        <div class="bg-secondary p-6 rounded-lg shadow-lg hover-scale transition duration-200">
          <h5 class="text-xl font-semibold text-gray-800">Harga Terjangkau</h5>
          <p class="text-gray-600 mt-2">Kami menawarkan harga yang sangat kompetitif tanpa mengorbankan kualitas layanan yang Anda terima.</p>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h5 class="font-semibold text-xl mb-4">Ikuti Kami</h5>
          <div class="flex justify-center md:justify-start space-x-6">
            <a href="#" class="text-white">Facebook</a>
            <a href="#" class="text-white">Instagram</a>
            <a href="#" class="text-white">Twitter</a>
          </div>
        </div>
        <div>
          <h5 class="font-semibold text-xl mb-4">Kontak</h5>
          <p>Pamayahan Blok Pinggir kali</p>
        </div>
      </div>
    </footer>
  </body>
</html>
