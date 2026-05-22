<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="Mindoro State University Alumni Network - Stay connected with your alma mater"
    />
    <title>Mindoro State University - Alumni Network</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap"
      rel="stylesheet"
    />

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/design-system.css?v=20260503.01" />
    <link rel="stylesheet" href="assets/css/main.css?v=20260411.24" />
    <link rel="stylesheet" href="assets/css/components.css?v=20260411.24" />
    <link rel="stylesheet" href="assets/css/responsive.css?v=20260518.03" />
    <link rel="stylesheet" href="assets/css/admin.css?v=20260517.01" />
    <link rel="stylesheet" href="assets/css/alumni.css?v=20260518.02" />
    <link rel="stylesheet" href="assets/css/enhancements.css?v=20260502.01" />
    <link rel="stylesheet" href="assets/css/dashboard-improved.css?v=20260517.01" />

    <!-- Firebase (for Google Auth) -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>
  </head>
  <body>
    <!-- App Container -->
    <div id="app">
      <!-- Loading State -->
      <div class="min-h-screen flex items-center justify-center">
        <div class="text-center">
          <div class="spinner spinner-lg mb-md"></div>
          <p class="text-secondary">Loading...</p>
        </div>
      </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Scripts -->
    <script src="assets/js/security-utils.js?v=20260503.01"></script>
    <script src="assets/js/session-manager.js?v=20260503.01"></script>
    <script src="assets/js/print-utils.js?v=20260503.01"></script>
    <script src="assets/js/api.js?v=20260518.06"></script>
    <script src="assets/js/router.js?v=20260514.04"></script>
    <script src="assets/js/auth.js?v=20260514.04"></script>
    <script src="assets/js/utils.js?v=20260513.01"></script>
    <script src="assets/js/validation.js?v=20260502.01"></script>
    <script src="assets/js/components.js?v=20260502.01"></script>
    <script src="assets/js/id-card-print.js?v=20260518.08"></script>
    <script src="assets/js/app.js?v=20260518.05"></script>
  </body>
</html>
