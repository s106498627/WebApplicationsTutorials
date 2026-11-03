<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Discover the very best in flash storage at Sandish">
    <meta name="keywords" content="Sandish, Flash, Storage, USB">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <script src="scripts/enhancements.js"></script>
    <title>Enhancements 2</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        
        <br>
        <h1>
            PHP / Maria Enhancement
        </h1>
        <br>
        <h2>
            The Database
        </h2>
        <p>
            I am concurrently studying "Data Management for the Big Data Age", where I was taught more advanced SQL
            techniques. I decided to put the study to practice and create the database in 3rd normal form (3NF). The
            advantage of 3NF is that it removes duplicated information by:

        </p>
        <ol>
            <li>Requiring singular values (no list in cells), and rows are unique. This is 1NF.</li>
            <li>Remove partial key dependencies, all rows must depend on the entire key. This is 2NF.</li>
            <li>Remove transitive dependencies, A &rarr; B &rarr; C is not allowed. This is 3NF.</li>
        </ol>
        <p>
            I worked in a docker container so that I could run up an environment with MariaDB and PHP locally without
            having to install anything natively. This has the upshot of creating a deterministic environment to develop
            in, ie anyone could clone my repo, run the container and start developing. The downside is when you rebuild
            a container, the state resets. I solved this by creating migrations that set up the DB tables and seed the
            DB for testing. This is a typical pattern in actual production software to set up and tear down DBs
            deterministically. I could run the migrations on load of the container, but found running the migrations
            manually fine for such a small project.

            I found the 3NF unergonomic and a large overhead for such a small project. But if this were a real project,
            as the scope of the project grows the DB will be easy to extend and reason about, rather than 1 massive
            table.
        </p>
        <br>
        <h2>
            DB Query in Cart
        </h2>
        <br>
        <p>
            I extended my <a href="enquire.php">cart</a> from part 2 by loading the prices of the product, SKU price,
            and cost of any extras from the DB. This has the advantage that When the prices change, the website does not
            need updating so the user will always see an up-to-date price. This uses the `get_product_prices()` and
            `get_extra_prices()` functions from query.php, which gets all the products and extras in each table.
        </p>
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>
