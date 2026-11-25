<?php

include 'config/db.php';
$products = $pdo->query("SELECT name, category, price, image, tag FROM products ORDER BY id")->fetchAll(PDO::FETCH_NUM);

$categories = [
    ["Fashion", "category-fashion.jpg"],
    ["Electronics", "category-electronics.jpg"],
    ["Accessories", "category-accessories.jpg"],
    ["Beauty", "category-beauty.jpg"],
    ["Footwear", "category-footwear.jpg"],
    ["Lifestyle", "category-lifestyle.jpg"]
];
?>
<?php
$pageTitle = "MyShop | Premium Online Shopping";
include 'header.php';
?>



    <!-- HERO SECTION -->

    <section class="hero-section">

        <div class="hero-overlay">

            <div class="hero-content">

                <span class="hero-label">THE PREMIUM COLLECTION</span>

                <h1>
                    Discover a Better
                    <span>Way to Shop.</span>
                </h1>
                <p>
                    Explore exceptional products, exclusive deals
                    and a shopping experience designed around you.
                    Find everything you love in one place.
                </p>

                <div class="hero-buttons">

                    <a href="#products" class="primary-button">
                        SHOP NOW
                    </a>

                    <a href="#categories" class="secondary-button">
                        EXPLORE CATEGORIES
                    </a>

                </div>

            </div>

        </div>

    </section>



    <!-- SHOPPING BENEFITS -->

    <section class="benefits-section">

        <div class="benefit-item">
            <div class="benefit-icon">🚚</div>
            <div>
                <h3>Fast Delivery</h3>
                <p>Convenient delivery options</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">🔒</div>
            <div>
                <h3>Secure Shopping</h3>
                <p>Your security matters</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">⭐</div>
            <div>
                <h3>Quality Products</h3>
                <p>Carefully selected items</p>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-icon">💬</div>
            <div>
                <h3>Customer Support</h3>
                <p>We're here to help</p>
            </div>
        </div>

    </section>



    <!-- CATEGORY SECTION -->

    <section class="category-section" id="categories">

        <div class="section-heading">

            <div>
                <span class="small-heading">EXPLORE OUR STORE</span>
                <h2>Shop By Category</h2>
                <p>Find exactly what you're looking for.</p>
            </div>

            <a href="#products" class="view-all">
                VIEW ALL →
            </a>

        </div>


        <div class="category-grid">

            <?php foreach ($categories as $category): ?>

                <a href="#products" class="category-card">

                    <img
                        src="assets/images/<?php echo htmlspecialchars($category[1]); ?>"
                        alt="<?php echo htmlspecialchars($category[0]); ?>"
                    >

                    <div class="category-overlay">
                        <h3><?php echo htmlspecialchars($category[0]); ?></h3>
                        <span>EXPLORE COLLECTION →</span>
                    </div>

                </a>

            <?php endforeach; ?>

        </div>

    </section>



    <!-- FEATURED PRODUCTS -->

    <section class="product-section" id="products">

        <div class="section-heading">

            <div>
                <span class="small-heading">HANDPICKED FOR YOU</span>
                <h2>Our Featured Products</h2>
                <p>Discover some of our most popular selections.</p>
            </div>

            <a href="#all-products" class="view-all">
                VIEW ALL PRODUCTS →
            </a>

        </div>


        <div class="product-grid">

            <?php foreach (array_slice($products, 0, 8) as $product): ?>

                <div class="product-card">

                    <div class="product-image">

                        <img
                            src="assets/images/<?php echo rawurlencode($product[3]); ?>"
                            alt="<?php echo htmlspecialchars($product[0]); ?>"
                        >

                        <span class="product-tag">
                            <?php echo htmlspecialchars($product[4]); ?>
                        </span>

                    </div>

                    <div class="product-details">

                        <span class="product-category">
                            <?php echo htmlspecialchars($product[1]); ?>
                        </span>
                        <h3><?php echo htmlspecialchars($product[0]); ?></h3>

                        <div class="product-rating">
                            ★★★★★
                        </div>

                        <div class="product-bottom">

                            <span class="product-price">
                                $<?php echo number_format($product[2]); ?>
                            </span>

                            <button type="button" class="add-cart" data-name="<?php echo htmlspecialchars($product[0]); ?>" data-price="<?php echo $product[2]; ?>" data-image="<?php echo htmlspecialchars($product[3]); ?>">
                                ADD +
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </section>



    <!-- PROMOTIONAL BANNER -->

    <section class="promotion-section" id="offers">

        <div class="promotion-content">

            <span>LIMITED TIME OFFER</span>

            <h2>Upgrade Your Style.</h2>

            <p>
                Explore our exclusive collection and discover
                amazing products at special promotional prices.
            </p>

            <a href="#all-products" class="primary-button">
                <div class="countdown-box">
    <span>OFFER ENDS IN:</span>
    <div class="countdown">
  <div><strong id="hours">0</strong><small>HRS</small></div>
  <div><strong id="minutes">0</strong><small>MIN</small></div>
  <div><strong id="seconds">0</strong><small>SEC</small></div>
</div>
</div>
                EXPLORE DEALS
            </a>

        </div>

        <div class="promotion-decoration">
            <div class="discount-circle">
                <strong>30%</strong>
                <span>OFF</span>
            </div>
        </div>

    </section>



    <!-- TRENDING PRODUCTS -->

    <section class="product-section trending-section" id="trending">

        <div class="section-heading">

            <div>
                <span class="small-heading">WHAT'S POPULAR</span>
                <h2>Trending Right Now</h2>
                <p>Take a look at products attracting attention.</p>
            </div>

        </div>


        <div class="product-grid">

            <?php foreach (array_slice($products, 8, 8) as $product): ?>

                <div class="product-card">

                    <div class="product-image">

                        <img
                            src="assets/images/<?php echo rawurlencode($product[3]); ?>"
                            alt="<?php echo htmlspecialchars($product[0]); ?>"
                        >

                        <span class="product-tag">
                            <?php echo htmlspecialchars($product[4]); ?>
                        </span>

                    </div>

                    <div class="product-details">

                        <span class="product-category">
                            <?php echo htmlspecialchars($product[1]); ?>
                        </span>

                        <h3><?php echo htmlspecialchars($product[0]); ?></h3>

                        <div class="product-rating">
                            ★★★★★
                        </div>

                        <div class="product-bottom">

                            <span class="product-price">
                                $<?php echo number_format($product[2]); ?>
                            </span>

                            <button type= "button" class="add-cart" data-name="<?php echo htmlspecialchars($product[0]); ?>" data-price="<?php echo $product[2]; ?>" data-image="<?php echo htmlspecialchars($product[3]); ?>">
                             ADD+
                        </div>

                    </div>

                </div>

            <?php endforeach; ?> 

        </div>

    </section>



    <!-- ABOUT SECTION -->

    <section class="about-section" id="about">

        <div class="about-content">

            <span class="small-heading">WELCOME TO MYSHOP</span>

            <h2>Shopping Should Be Simple, Enjoyable and Worth It.</h2>

            <p>
                At MyShop, we believe shopping should be more than
                just buying products. It should be an experience
                that brings convenience, quality and satisfaction.
            </p>

            <p>
                Our store brings together carefully selected
                products across different categories so that
                you can discover more of what you love.
            </p>
            <a href="#categories" class="primary-button">
                DISCOVER OUR STORE
            </a>

        </div>

        <div class="about-stats">

            <div class="stat-card">
                <h3>100+</h3>
                <p>Featured Products</p>
            </div>

            <div class="stat-card">
                <h3>06</h3>
                <p>Shopping Categories</p>
            </div>

            <div class="stat-card">
                <h3>100%</h3>
                <p>Customer Focus</p>
            </div>

        </div>

    </section>



    <!-- MORE PRODUCTS -->

    <section class="product-section more-products" id="all-products">

        <div class="section-heading">

            <div>
                <span class="small-heading">MORE TO DISCOVER</span>
                <h2>Explore More Products</h2>
                <p>There's always something new to discover.</p>
            </div>

        </div>


        <div class="product-grid">

            <?php foreach (array_slice($products, 16) as $product): ?>

                <div class="product-card">

                    <div class="product-image">

                        <img
                            src="assets/images/<?php echo rawurlencode($product[3]); ?>"
                            alt="<?php echo htmlspecialchars($product[0]); ?>"
                        >

                        <span class="product-tag">
                            <?php echo htmlspecialchars($product[4]); ?>
                        </span>

                    </div>

                    <div class="product-details">

                        <span class="product-category">
                            <?php echo htmlspecialchars($product[1]); ?>
                        </span>

                        <h3><?php echo htmlspecialchars($product[0]); ?></h3>

                        <div class="product-rating">
                            ★★★★★
                        </div>

                        <div class="product-bottom">

                            <span class="product-price">
                                $<?php echo number_format($product[2]); ?>
                            </span>

                            <button type="button" class="add-cart" data-name="<?php echo htmlspecialchars($product[0]); ?>" data-price="<?php echo $product[2]; ?>" data-image="<?php echo htmlspecialchars($product[3]); ?>">
                                ADD +
                            </button>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </section>



    <!-- NEWSLETTER SECTION -->

    <section class="newsletter-section">

        <div class="newsletter-content">

            <span>STAY IN THE LOOP</span>

            <h2>Get Updates on New Arrivals and Exclusive Offers.</h2>

            <p>
                Be among the first to discover new collections,
                promotions and special shopping opportunities.
            </p>

            <form action="subscribe.php" method="POST" class="newsletter-form" id="newsletterForm">
<input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" class="nl-trap">

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email address"
                    required
                >

                <button type="submit">
                    SUBSCRIBE
                </button>

            </form>

        </div>

    </section>

    <?php include 'footer.php'; ?>