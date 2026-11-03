<?php
// Description: Homepage for Sandish Site
// Author: Charlie Hutcheson
// Date: 2026-11-03
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Discover the very best in flash storage at Sandish">
    <meta name="keywords" content="Sandish, Flash, Storage, USB">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-hero.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <title>Sandish</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <!-- BEGIN Hero -->
        <section class="hero">
            <div class="hero-box">
                <h1>Moving data at the speed of thought</h1>
                <p>
                    Next-generation flash storage. Transfer 4K movies in seconds.
                </p>
            </div>
        </section>
        <!-- END Hero -->
        <br>
        <h2>
            Discover your next digital storage solution.
        </h2>
        <p>
            Explore the full Sandish lineup to find the perfect mix of pocket-sized speed, high-capacity storage, and
            rugged.
        </p>
        <br>

        <h2>
            Why Choose Sandish?
        </h2>
        <ul>
            <li>Lightning speeds</li>
            <li>Massive storage</li>
            <li>Drop-proof</li>
            <li>Sleek designs</li>
        </ul>
        <br>
        <p>
            Ready to Upgrade? Click below to browse our range and find your match.
        </p>
        <br>
        <div class="center-container">
            <a class="button-link" href="product.html">Explore products</a>
        </div>
        <br>
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>