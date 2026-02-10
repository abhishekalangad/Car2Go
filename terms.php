<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Terms of Service - CAR2GO';
include 'templates/header.php';
?>

<style>
    .page-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 6rem 0 8rem;
        color: white;
        text-align: center;
        position: relative;
    }

    .legal-content {
        background: white;
        border-radius: 16px;
        padding: 4rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        margin-top: -80px;
        position: relative;
        z-index: 10;
        margin-bottom: 4rem;
        line-height: 1.8;
        color: #475569;
    }

    .legal-content h2 {
        color: #1e293b;
        font-weight: 800;
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
    }

    .legal-content ul {
        margin-bottom: 2rem;
        padding-left: 1.5rem;
    }

    .legal-content li {
        margin-bottom: 0.75rem;
    }

    .last-updated {
        font-size: 0.9rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }
</style>

<div class="page-hero">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-3">Terms of Service</h1>
        <p class="last-updated">Last Updated:
            <?php echo date('F d, Y'); ?>
        </p>
    </div>
</div>

<div class="container">
    <div class="legal-content">
        <p class="lead text-dark mb-5 font-weight-bold">
            Welcome to CAR2GO. By accessing or using our platform, you agree to be bound by these Terms of Service.
            Please read them carefully.
        </p>

        <h2>1. User Accounts</h2>
        <p>To use most features of the Platform, you must register for an account. you agree to:</p>
        <ul>
            <li>Provide accurate, current, and complete information during registration.</li>
            <li>Maintain the security of your password and accept all risks of unauthorized access to your account.</li>
            <li>Notify us immediately if you discover or suspect any security breaches related to the Platform.</li>
        </ul>

        <h2>2. Services Provided</h2>
        <p>CAR2GO provides a platform for:</p>
        <ul>
            <li><strong>Rentals:</strong> Connecting car owners with users wishing to rent vehicles.</li>
            <li><strong>Drivers:</strong> Connecting professional drivers with users needing chauffeur services.</li>
            <li><strong>Maintenance:</strong> Connecting vehicle owners with authorized service centers.</li>
        </ul>

        <h2>3. Booking and Payments</h2>
        <p>
            All bookings are subject to availability. You agree to pay all charges associated with your booking,
            including rental fees, driver fees, and any applicable taxes. We use secure third-party payment processors
            for all transactions.
        </p>

        <h2>4. User Conduct</h2>
        <p>You agree not to:</p>
        <ul>
            <li>Use the Platform for any illegal purpose or in violation of any local, state, or national law.</li>
            <li>Harass, threaten, or defraud other users or CAR2GO staff.</li>
            <li>Damage, disable, overburden, or impair the Platform or interfere with any other party's use of the
                Platform.</li>
        </ul>

        <h2>5. Limitation of Liability</h2>
        <p>
            To the fullest extent permitted by law, CAR2GO shall not be liable for any indirect, incidental, special,
            consequential, or punitive damages, or any loss of profits or revenues.
        </p>

        <h2>6. Changes to Terms</h2>
        <p>
            We reserve the right to modify these Terms at any time. We will provide notice of significant changes by
            updating the date at the top of these Terms and, where appropriate, by other means.
        </p>

        <h2>7. Contact</h2>
        <p>Questions about the Terms of Service should be sent to us at:</p>
        <p class="font-weight-bold text-dark">
            <i class="fas fa-envelope mr-2"></i> legal@car2go.com<br>
        </p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>