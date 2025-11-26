<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

    <title>Retete - Fitness Studio</title>

    <!-- Additional CSS Files -->
    <link rel="stylesheet" type="text/css" href="./../assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./../assets/css/font-awesome.css">
    <link rel="stylesheet" href="./../assets/css/templatemo-training-studio.css">
</head>

<body>

    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="<?php echo defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'; ?>" class="logo">Fitness<em> Studio</em></a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <li><a href="<?php echo defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'; ?>">Home</a></li>
                            <li><a href="#" class="active">Retete</a></li>
                            <li class="main-button">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="<?php echo defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'; ?>logout">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a>
                                <?php else: ?>
                                    <a href="<?php echo defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'; ?>">Log In</a>
                                <?php endif; ?>
                            </li>
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Retete Section Start ***** -->
    <section class="section" id="retete" style="margin-top: 120px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading">
                        <h2>Retete <em>Sanatoase</em></h2>
                        <img src="./../assets/images/line-dec.png" alt="waves">
                        <p>Descopera retete delicioase si nutritive pentru un stil de viata sanatos.</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-content">
                        <h3>Aici vom implementa sectiunea de retete</h3>
                        <p>Aceasta pagina va contine in curand o colectie completa de retete sanatoase.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Retete Section End ***** -->

    <!-- ***** Footer Start ***** -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; 2025 M.N.C. SoftwareStudio - Designed with <a rel="nofollow" href="https://templatemo.com" class="tm-text-link" target="_parent">TemplateMo</a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="./../assets/js/jquery-2.1.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="./../assets/js/popper.js"></script>
    <script src="./../assets/js/bootstrap.min.js"></script>

    <!-- Plugins -->
    <script src="./../assets/js/scrollreveal.min.js"></script>
    <script src="./../assets/js/waypoints.min.js"></script>
    <script src="./../assets/js/jquery.counterup.min.js"></script>
    <script src="./../assets/js/imgfix.min.js"></script>
    <script src="./../assets/js/mixitup.js"></script>
    <script src="./../assets/js/accordions.js"></script>

    <!-- Global Init -->
    <script src="./../assets/js/custom.js"></script>

</body>
</html>
