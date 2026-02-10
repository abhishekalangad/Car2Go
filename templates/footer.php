</main>
<!-- End Main Content -->

<!-- Premium Footer -->
<footer id="premium-footer">
    <div class="container">
        <div class="row">
            <!-- Brand & Story -->
            <div class="col-md-4 col-sm-12 mb-4">
                <a href="<?php echo $base_url ?? '/'; ?>" class="footer-brand">
                    <i class="fa fa-car mr-2"></i> CAR2GO
                </a>
                <p class="footer-desc">
                    Defining the future of mobility. Whether you need a premium rental, a professional chauffeur, or
                    expert service, we are your trusted partner on every journey.
                </p>
                <div class="social-icons">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                    <a href="#"><i class="fa fa-twitter"></i></a>
                    <a href="#"><i class="fa fa-instagram"></i></a>
                    <a href="#"><i class="fa fa-linkedin"></i></a>
                </div>
            </div>

            <!-- Links -->
            <div class="col-md-2 col-sm-4 col-xs-6 mb-4">
                <h5 class="footer-heading">Platform</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base_url; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base_url; ?>viewcars.php">Rent a Car</a></li>
                    <li><a href="<?php echo $base_url; ?>viewdriv.php">Hire Drivers</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-2 col-sm-4 col-xs-6 mb-4">
                <h5 class="footer-heading">Support</h5>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>contact.php">Contact Us</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-4 col-sm-4 col-xs-12">
                <h5 class="footer-heading">Be the First to Know</h5>
                <p class="small text-muted mb-2">Subscribe for latest updates and offers.</p>
                <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Subscribed!');">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter email address" required>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </span>
                    </div>
                </form>
                <div class="secure-badge">
                    <i class="fa fa-lock text-success"></i> Secure & Spam-free
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row">
                <div class="col-sm-6 copyright">
                    &copy; <?php echo date('Y'); ?> <strong>CAR2GO Inc.</strong> All rights reserved.
                </div>
                <div class="col-sm-6 text-right-sm">
                    <span class="language-select"><i class="fa fa-globe"></i> English (US)</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Premium Footer CSS (Bootstrap 3 Compatible) */
    #premium-footer {
        background-color: #0f172a;
        color: #94a3b8;
        padding: 60px 0 20px;
        font-size: 14px;
        margin-top: 50px;
        width: 100%;
    }

    #premium-footer .footer-brand {
        display: block;
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 15px;
        text-decoration: none;
    }

    #premium-footer .footer-desc {
        color: #cbd5e1;
        line-height: 1.6;
        margin-bottom: 20px;
        max-width: 300px;
    }

    #premium-footer .social-icons a {
        display: inline-block;
        width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: #fff;
        margin-right: 10px;
        transition: all 0.3s;
    }

    #premium-footer .social-icons a:hover {
        background: #2563eb;
        text-decoration: none;
    }

    #premium-footer .footer-heading {
        color: #fff;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    #premium-footer .footer-links {
        list-style: none;
        padding: 0;
    }

    #premium-footer .footer-links li {
        margin-bottom: 10px;
    }

    #premium-footer .footer-links a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.2s;
    }

    #premium-footer .footer-links a:hover {
        color: #fff;
        padding-left: 5px;
    }

    /* Newsletter */
    #premium-footer .newsletter-form .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        height: 42px;
    }

    #premium-footer .newsletter-form .btn-primary {
        background: #2563eb;
        border: none;
        height: 42px;
        font-weight: 600;
        padding: 0 20px;
    }

    #premium-footer .newsletter-form .btn-primary:hover {
        background: #1d4ed8;
    }

    #premium-footer .secure-badge {
        font-size: 12px;
        margin-top: 10px;
        color: #64748b;
    }

    /* Bottom Bar */
    #premium-footer .footer-bottom {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 13px;
    }

    @media (min-width: 768px) {
        .text-right-sm {
            text-align: right;
        }
    }
</style>

<!-- JavaScript -->
<script src="<?php echo $base_url; ?>js/jquery-2.1.4.min.js"></script>
<script src="<?php echo $base_url; ?>js/bootstrap.js"></script>

<!-- Clean up any flash messages -->
<script>
    setTimeout(function () { $('.alert').fadeOut(); }, 4000);
</script>

</body>

</html>
<?php
// Close database connection if exists
if (isset($con) && $con instanceof mysqli) {
    @$con->close();
}
?>