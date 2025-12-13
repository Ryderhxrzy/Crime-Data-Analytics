<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/admin-header.css">
    <link rel="stylesheet" href="../css/hero.css">
    <link rel="stylesheet" href="../css/sidebar-footer.css">
</head>
<body>
    <?php include '../includes/sidebar.php' ?>

    <?php include '../includes/admin-header.php'; ?>

    <div class="main-content">
        <div class="main-container">
            <div class="title">
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <ol class="breadcrumb-list">
                        <li class="breadcrumb-item">
                            <a href="/" class="breadcrumb-link">
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="/components" class="breadcrumb-link">
                                <span>Users</span>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <span>Template</span>
                        </li>
                    </ol>
                </nav>
                <h1>Template</h1>
                <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Voluptate aliquid natus eum id, excepturi rerum delectus consequatur ipsam repudiandae deserunt blanditiis provident magni et explicabo deleniti dignissimos ullam? Magnam sed similique odio, voluptatem adipisci ut sunt possimus consequatur non totam esse quod laboriosam sequi, voluptatum nam quidem cumque quis quam!</p>
            </div>
            
            <div class="sub-container">
                <div class="page-content">
                    <!--Insert content here-->
                </div>
            </div>
        </div>
        <?php /*include('../includes/admin-footer.php')*/ ?>
    </div>
</body>
</html>