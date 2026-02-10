<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Privacy Policy - CAR2GO';
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

    .legal-content h5 {
        color: #334155;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
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
        <h1 class="display-4 font-weight-bold mb-3">Privacy Policy</h1>
        <p class="last-updated">Last Updated:
            <?php echo date('F d, Y'); ?>
        </p>
    </div>
</div>

<div class="container">
    <div class="legal-content">
        <p class="lead text-dark mb-5 font-weight-bold">
            At CAR2GO, your privacy is our priority. We are committed to protecting your personal data and ensuring
            strict compliance with all applicable data protection laws.
        </p>

        <h2>1. Information We Collect</h2>
        <p>We collect information to provide better services to all our users. The types of information we collect
            include:</p>
        <ul>
            <li><strong>Personal Information:</strong> Name, email address, phone number, and government identification
                (Driving License, Aadhaar for drivers).</li>
            <li><strong>Vehicle Information:</strong> For car owners, we collect vehicle details, registration
                documents, and insurance papers.</li>
            <li><strong>Usage Data:</strong> Information about how you use our app, including booking history, search
                queries, and location data during active trips.</li>
        </ul>

        <h2>2. How We Use Information</h2>
        <p>We use the information we collect for the following purposes:</p>
        <ul>
            <li>To facilitate car rentals, driver bookings, and service appointments.</li>
            <li>To verify the identity of drivers and car owners for safety.</li>
            <li>To improve our platform, develop new features, and enhance user experience.</li>
            <li>To communicate with you regarding updates, offers, and security notices.</li>
        </ul>

        <h2>3. Data Sharing</h2>
        <p>We do not sell your personal data. We only share information in the following limited circumstances:</p>
        <ul>
            <li><strong>With Partners:</strong> Your booking details are shared with the specific driver, car owner, or
                service center fulfilling your request.</li>
            <li><strong>For Legal Reasons:</strong> We may share information if required by law or to protect the safety
                of our users and the public.</li>
        </ul>

        <h2>4. Data Security</h2>
        <p>
            We implement high-standard security measures, including encryption and secure server infrastructure, to
            protect your data against unauthorized access, alteration, disclosure, or destruction.
        </p>

        <h2>5. Your Rights</h2>
        <p>
            You have the right to access, correct, or delete your personal information. You can manage your profile
            settings directly through your dashboard or contact our support team for assistance.
        </p>

        <h2>6. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact us at:</p>
        <p class="font-weight-bold text-dark">
            <i class="fas fa-envelope mr-2"></i> privacy@car2go.com<br>
            <i class="fas fa-building mr-2"></i> CAR2GO Legal Dept, 123 Luxury Drive, Metro City
        </p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>