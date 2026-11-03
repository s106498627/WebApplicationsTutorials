<!-- Description: Order form for all products -->
<!-- Author: Charlie Hutcheson -->
<!-- Date: 2026-11-03 -->

<?php
session_start();
require_once("lib/helpers.php");
require_once("lib/query.php");

// get back fields and errors from process_order
$enquireErrors = isset($_SESSION["enquire_errors"]) ? $_SESSION["enquire_errors"] : [];
$posted = isset($_SESSION["enquire_post"]) ? $_SESSION["enquire_post"] : [];

// reset sendback info for next submission
unset($_SESSION["enquire_errors"]);
unset($_SESSION["enquire_post"]);

// cart prices come from the products table
$conn = db_connect();
$prices = get_product_prices($conn);
$extraPrices = get_extra_prices($conn);
if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Discover the very best in flash storage at Sandish">
    <meta name="keywords" content="Sandish, Flash, Storage, USB">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/style-form.css">
    <link rel="stylesheet" href="styles/style-cart.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <script src="scripts/part2.js"></script>
    <script src="scripts/enhancements.js"></script>
    <title>Enquiries</title>
</head>

<body>
    <?php include_once("components/navbar.inc.php") ?>

    <main>
        <br>
        <h1>Order &amp; Enquiry</h1>
        <!-- validation done by payment.php. novalidate stops the browser doing simple checks
         POST not GET to not leak form inputs to the address bar. php sends you back here if there are errors. -->

        <!-- NOTE: values in this form are re-populated if there were any errors from the posted values,
         the cart cards are rebuilt by enhancements.js from what part2.js saved in localStorage -->
        <form id="enquire-form" action="payment.php" method="post" novalidate>
            <!-- BEGIN Left Column -->
            <div class="form-col">
                <h2><span class="icons-person"></span>Details</h2>
                <div>
                    <label for="first-name">First name</label>
                    <div class="field">
                        <input type="text" id="first-name" name="first-name" value="<?php echo get("first-name", $posted) ?>">
                    </div>
                </div>
                <div>
                    <label for="last-name">Last name</label>
                    <div class="field">
                        <input type="text" id="last-name" name="last-name" value="<?php echo get("last-name", $posted) ?>">
                    </div>
                </div>
                <div>
                    <label for="email">Email address</label>
                    <div class="field">
                        <input type="text" id="email" name="email" value="<?php echo get("email", $posted) ?>" inputmode="email">
                    </div>
                </div>

                <fieldset>
                    <legend>Address</legend>
                    <div>
                        <!-- NOTE: cannot enter unit number for townhouse or apartment ie 2/22 asc street. Letter of the assignment spec. -->
                        <label for="street-address">Street address</label>
                        <div class="field">
                            <input type="text" id="street-address" name="street-address" value="<?php echo get("street-address", $posted) ?>">
                        </div>
                    </div>
                    <div>
                        <label for="suburb">Suburb/town</label>
                        <div class="field">
                            <input type="text" id="suburb" name="suburb" value="<?php echo get("suburb", $posted) ?>">
                        </div>
                    </div>
                    <div>
                        <label for="state">State</label>
                        <select id="state" name="state">
                            <option value="" selected>-- Choose an option --</option>
                            <option value="ACT" <?php echo get_selected("state", $posted, "ACT") ?>>ACT</option>
                            <option value="NSW" <?php echo get_selected("state", $posted, "NSW") ?>>NSW</option>
                            <option value="NT" <?php echo get_selected("state", $posted, "NT") ?>>NT</option>
                            <option value="QLD" <?php echo get_selected("state", $posted, "QLD") ?>>QLD</option>
                            <option value="SA" <?php echo get_selected("state", $posted, "SA") ?>>SA</option>
                            <option value="TAS" <?php echo get_selected("state", $posted, "TAS") ?>>TAS</option>
                            <option value="VIC" <?php echo get_selected("state", $posted, "VIC") ?>>VIC</option>
                            <option value="WA" <?php echo get_selected("state", $posted, "WA") ?>>WA</option>
                        </select>
                    </div>
                    <div>
                        <label for="postcode">Postcode</label>
                        <div class="field">
                            <input type="text" id="postcode" name="postcode" value="<?php echo get("postcode", $posted) ?>" inputmode="numeric">
                        </div>
                    </div>
                </fieldset>

                <div>
                    <label for="phone">Phone number</label>
                    <div class="field">
                        <input type="text" id="phone" name="phone" value="<?php echo get("phone", $posted) ?>" inputmode="numeric" placeholder="0123 456 789">
                    </div>
                </div>

                <fieldset>
                    <legend>Preferred contact method</legend>
                    <label>
                        <input type="radio" name="preferred-contact" value="email" <?php echo get_checked("preferred-contact", $posted, "email", "email") ?>>
                        Email
                    </label>
                    <label>
                        <input type="radio" name="preferred-contact" value="post" <?php echo get_checked("preferred-contact", $posted, "post") ?>>
                        Post
                    </label>
                    <label>
                        <input type="radio" name="preferred-contact" value="phone" <?php echo get_checked("preferred-contact", $posted, "phone") ?>>
                        Phone
                    </label>
                </fieldset>
            </div>
            <!-- END Left Column -->
            <!-- BEGIN Right Column -->
            <div class="form-col">
                <!-- BEGIN Cart -->
                <section id="cart" class="cart">
                    <h2><span class="icons-cart"></span>Cart</h2>
                    <!-- after a php error enhancements.js rebuilds the cards from what part2.js saved in localStorage -->
                    <div id="cart-items" class="cart-list">
                        <!-- add item card, always  last box in list -->
                        <button type="button" id="add-item" class="cart-add">
                            <span class="cart-add-icon icons-add"></span>
                            <span class="cart-add-text">Add item</span>
                        </button>
                    </div>
                </section>

                <!-- NOTE: see https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/template
                 fragment for the cart items above. used in enhancements.js to add another item
                 when hitting the "add" button -->
                <template id="cart-item-template">
                    <section class="cart-item cart-card">
                        <div class="cart-card-header">
                            <h3 class="cart-card-title">Item <span class="cart-item-number"></span></h3>
                            <button type="button" class="icon-button cart-remove"><span
                                    class="icons-delete"></span></button>
                        </div>

                        <p class="cart-label">Product</p>
                        <div class="cart-card-group" role="radiogroup">
                            <label class="cart-card-group-item">
                                <!-- see: https://developer.mozilla.org/en-US/docs/Web/HTML/How_to/Use_data_attributes
                                used to get input field type in the js by getAttribute -->
                                <input type="radio" value="pokemon-usb" data-field="product">
                                <span class="cart-card-group-item-body">
                                    <span class="cart-card-group-item-name">Pokémon USB Drive</span>
                                    <span class="cart-card-group-item-price"><?php echo from_price($prices, "pokemon-usb") ?></span>
                                </span>
                            </label>
                            <label class="cart-card-group-item">
                                <input type="radio" value="extreme-usb" data-field="product">
                                <span class="cart-card-group-item-body">
                                    <span class="cart-card-group-item-name">Extreme Encrypted USB Drive</span>
                                    <span class="cart-card-group-item-price"><?php echo from_price($prices, "extreme-usb") ?></span>
                                </span>
                            </label>
                            <label class="cart-card-group-item">
                                <input type="radio" value="classic-usb-a" data-field="product">
                                <span class="cart-card-group-item-body">
                                    <span class="cart-card-group-item-name">Classic USB-A Drive</span>
                                    <span class="cart-card-group-item-price"><?php echo from_price($prices, "classic-usb-a") ?></span>
                                </span>
                            </label>
                        </div>

                        <div class="cart-field-row">
                            <label class="cart-field cart-field-select">
                                <!-- enhancements.js adds the price to each option once a product is picked -->
                                <select data-field="capacity">
                                    <option value="" selected>Choose storage</option>
                                    <option value="32gb"<?php echo sku_price_attrs($prices, "32gb") ?>>32 GB</option>
                                    <option value="64gb"<?php echo sku_price_attrs($prices, "64gb") ?>>64 GB</option>
                                    <option value="128gb"<?php echo sku_price_attrs($prices, "128gb") ?>>128 GB</option>
                                    <option value="256gb"<?php echo sku_price_attrs($prices, "256gb") ?>>256 GB</option>
                                </select>
                                <span class="cart-field-label">Storage</span>
                            </label>
                            <label class="cart-field cart-field-quantity">
                                <input type="text" inputmode="numeric" value="1" data-field="quantity">
                                <span class="cart-field-label">Quantity</span>
                            </label>
                        </div>
                    </section>
                </template>
                <!-- END Cart -->

                <!-- optional extras apply once to the whole order, no selection required -->
                <fieldset>
                    <legend>Order extras</legend>
                    <label>
                        <input type="checkbox" name="extras[]" value="express-shipping" <?php echo get_checked("extras", $posted, "express-shipping") ?>>
                        Express shipping<?php echo extra_price($extraPrices, "express-shipping") ?>
                    </label>
                    <label>
                        <input type="checkbox" name="extras[]" value="gift-wrap" <?php echo get_checked("extras", $posted, "gift-wrap") ?>>
                        Gift wrapping<?php echo extra_price($extraPrices, "gift-wrap") ?>
                    </label>
                    <label>
                        <input type="checkbox" name="extras[]" value="extended-warranty" <?php echo get_checked("extras", $posted, "extended-warranty") ?>>
                        Extended 3-year warranty<?php echo extra_price($extraPrices, "extended-warranty") ?>
                    </label>
                </fieldset>

                <div>
                    <label for="comment">Comment</label>
                    <textarea id="comment" name="comment"
                        placeholder="Enter other instructions or requests if desired..."><?php echo get("comment", $posted) ?></textarea>
                </div>

                <!-- part2.js populates this with any errors in form, or php if js was skipped -->
                <?php echo create_err_box($enquireErrors) ?>

                <button type="submit" class="button-link">Pay Now</button>
            </div>
            <!-- END Right Column -->
        </form>
    </main>

    <?php include_once("components/footer.inc.php") ?>
</body>

</html>
