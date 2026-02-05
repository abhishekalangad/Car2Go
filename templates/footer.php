</main>
<!-- End Main Content -->

<!-- Footer -->
<footer class="bg-dark text-white mt-5">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-car"></i> CAR2GO</h5>
                <p class="text-muted">
                    Your trusted platform for car rentals, driver bookings, and vehicle services.
                </p>
            </div>

            <div class="col-md-2">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-muted">Home</a></li>
                    <li><a href="/about.php" class="text-muted">About Us</a></li>
                    <li><a href="/contact.php" class="text-muted">Contact</a></li>
                    <li><a href="/faq.php" class="text-muted">FAQ</a></li>
                </ul>
            </div>

            <div class="col-md-2">
                <h6>Services</h6>
                <ul class="list-unstyled">
                    <li><a href="/viewcars.php" class="text-muted">Rent a Car</a></li>
                    <li><a href="/viewdriv.php" class="text-muted">Book a Driver</a></li>
                    <li><a href="/viewservicee1.php" class="text-muted">Car Services</a></li>
                </ul>
            </div>

            <div class="col-md-2">
                <h6>For Partners</h6>
                <ul class="list-unstyled">
                    <li><a href="/rentingform.php" class="text-muted">List Your Car</a></li>
                    <li><a href="/driverreg.php" class="text-muted">Register as Driver</a></li>
                    <li><a href="/servicereg.php" class="text-muted">Partner with Us</a></li>
                </ul>
            </div>

            <div class="col-md-2">
                <h6>Connect</h6>
                <div class="social-links">
                    <a href="#" class="text-white mr-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white mr-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white mr-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        <hr class="bg-secondary">

        <div class="row">
            <div class="col-md-6 text-center text-md-left">
                <p class="mb-0 text-muted">
                    &copy;
                    <?php echo date('Y'); ?> CAR2GO. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-right">
                <a href="/privacy.php" class="text-muted mr-3">Privacy Policy</a>
                <a href="/terms.php" class="text-muted">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery (required for Bootstrap) -->
<script src="<?php echo $base_url ?? '/'; ?>js/jquery.min.js"></script>

<!-- Bootstrap JS -->
<script src="<?php echo $base_url ?? '/'; ?>js/bootstrap.bundle.js"></script>

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