<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Crime Data Analytics</title>
    <link rel="stylesheet" href="frontend/css/global.css">
    <link rel="stylesheet" href="frontend/css/404.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="frontend/image/favicon.ico">
</head>
<body>
    <div class="error-page-wrapper">
        <div class="not-found-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <h1 class="error-code">404</h1>
            <h2 class="error-title">Page Not Found</h2>
            <p class="error-message">
                Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or the URL might be incorrect.
            </p>

            <div class="btn-container">
                <a href="javascript:history.back()" class="error-btn error-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Go Back
                </a>
                <a href="index.php" class="error-btn error-btn-primary">
                    <i class="fas fa-home"></i>
                    Back to Home
                </a>
            </div>

            <div class="suggestions">
                <h3 class="suggestions-title">You might want to try:</h3>
                <ul class="suggestions-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <a href="index.php">Login to your account</a>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Double-check the URL for typos</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Contact the administrator if you believe this is an error</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
