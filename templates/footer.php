</main>
<!-- End Main Content -->

<!-- Footer -->
<footer style="background: var(--bg-dark); color: white; margin-top: 5rem; padding: 4rem 0 2rem;">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-4" style="font-weight: 700; color: var(--primary-color);">
                    <i class="fas fa-car"></i> CAR2GO
                </h5>
                <p class="text-muted">
                    Defining the future of mobility. Your trusted partner for premium car rentals, professional drivers,
                    and expert vehicle solutions.
                </p>
                <div class="mt-4">
                    <a href="#" class="btn btn-sm btn-outline-light mr-2 rounded-circle"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light mr-2 rounded-circle"><i
                            class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-light mr-2 rounded-circle"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-md-2 mb-4">
                <h6 class="text-uppercase mb-4 font-weight-bold">Explore</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/" class="text-muted">Home</a></li>
                    <li class="mb-2"><a href="/about.php" class="text-muted">About Us</a></li>
                    <li class="mb-2"><a href="/contact.php" class="text-muted">Contact</a></li>
                    <li class="mb-2"><a href="/faq.php" class="text-muted">FAQ</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase mb-4 font-weight-bold">Our Services</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/viewcars.php" class="text-muted">Premium Car Rental</a></li>
                    <li class="mb-2"><a href="/viewdriv.php" class="text-muted">Expert Driver Booking</a></li>
                    <li class="mb-2"><a href="/viewservicee1.php" class="text-muted">Authorized Service Centers</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h6 class="text-uppercase mb-4 font-weight-bold">Contact Info</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2"><i class="fas fa-map-marker-alt mr-2"></i> 123 Luxury Drive, Metro City</li>
                    <li class="mb-2"><i class="fas fa-phone mr-2"></i> +1 (234) 567-890</li>
                    <li class="mb-2"><i class="fas fa-envelope mr-2"></i> support@car2go.com</li>
                </ul>
            </div>
        </div>

        <hr style="border-top: 1px solid rgba(255,255,255,0.1); margin: 3rem 0 2rem;">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-left">
                <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                    &copy; <?php echo date('Y'); ?> <strong>CAR2GO</strong>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-right mt-3 mt-md-0">
                <a href="/privacy.php" class="text-muted mr-3" style="font-size: 0.9rem;">Privacy</a>
                <a href="/terms.php" class="text-muted" style="font-size: 0.9rem;">Terms</a>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="<?php echo $base_url; ?>js/jquery-2.1.4.min.js"></script>
<script src="<?php echo $base_url; ?>js/bootstrap.js"></script>

<!-- Additional JS (if any) -->
<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo ($base_url ?? '/') . $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Custom Scripts -->
<script>
    // Auto-hide flash messages after 5 seconds
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Add active class to current nav item
    $(document).ready(function () {
        var currentPage = window.location.pathname;
        $('.navbar-nav a').each(function () {
            var href = $(this).attr('href');
            if (currentPage.indexOf(href) !== -1 && href !== '/') {
                $(this).parent().addClass('active');
            }
        });
    });
</script>
</body>

</html>
<?php
// Close database connection if exists
if (isset($con) && $con instanceof mysqli) {
    $con->close();
}
?>