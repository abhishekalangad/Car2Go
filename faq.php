<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Frequently Asked Questions - CAR2GO';
include 'templates/header.php';
?>

<style>
    .page-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 6rem 0 8rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('images/bg3.jpg') center/cover;
        opacity: 0.15;
        z-index: 0;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .faq-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-top: -60px;
        position: relative;
        z-index: 10;
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .accordion .card {
        border: none;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 0;
    }

    .accordion .card-header {
        background: white;
        border: none;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .accordion .card-header:hover {
        background: #f8fafc;
    }

    .accordion .btn-link {
        color: #1e293b;
        font-weight: 700;
        text-decoration: none;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        width: 100%;
        text-align: left;
    }

    .accordion .btn-link:hover {
        text-decoration: none;
        color: #3b82f6;
    }

    .accordion .card-body {
        padding: 0 1.5rem 1.5rem 1.5rem;
        color: #64748b;
        line-height: 1.7;
    }

    .icon-box {
        width: 40px;
        height: 40px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
</style>

<div class="page-hero">
    <div class="container hero-content">
        <h1 class="display-4 font-weight-bold mb-3">FAQ</h1>
        <p class="lead opacity-8 mx-auto" style="max-width: 600px;">
            Common questions about renting, driving, and services.
        </p>
    </div>
</div>

<div class="container mb-5">
    <div class="faq-card">
        <div class="row">
            <div class="col-lg-3 bg-light p-5 border-right">
                <h5 class="font-weight-bold mb-4">Categories</h5>
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active mb-2 font-weight-bold rounded-lg py-3" id="v-pills-gen-tab"
                        data-toggle="pill" href="#v-pills-gen" role="tab"><i class="fas fa-info-circle mr-2"></i>
                        General</a>
                    <a class="nav-link mb-2 font-weight-bold rounded-lg py-3" id="v-pills-rent-tab" data-toggle="pill"
                        href="#v-pills-rent" role="tab"><i class="fas fa-car mr-2"></i> Renting</a>
                    <a class="nav-link mb-2 font-weight-bold rounded-lg py-3" id="v-pills-drive-tab" data-toggle="pill"
                        href="#v-pills-drive" role="tab"><i class="fas fa-user-tie mr-2"></i> Drivers</a>
                    <a class="nav-link mb-2 font-weight-bold rounded-lg py-3" id="v-pills-safe-tab" data-toggle="pill"
                        href="#v-pills-safe" role="tab"><i class="fas fa-shield-alt mr-2"></i> Safety</a>
                </div>
            </div>

            <div class="col-lg-9 p-5">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- General -->
                    <div class="tab-pane fade show active" id="v-pills-gen" role="tabpanel">
                        <h4 class="font-weight-bold mb-4 text-dark">General Questions</h4>
                        <div class="accordion" id="accordionGen">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapseOne">
                                            <div class="icon-box"><i class="fas fa-question"></i></div>
                                            What is CAR2GO?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseOne" class="collapse show" data-parent="#accordionGen">
                                    <div class="card-body">
                                        CAR2GO is a premium platform connecting vehicle owners, professional drivers,
                                        and service centers with users. We offer seamless car rentals, chauffeur
                                        services, and vehicle maintenance booking.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                            data-target="#collapseTwo">
                                            <div class="icon-box"><i class="fas fa-user-plus"></i></div>
                                            How do I sign up?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" data-parent="#accordionGen">
                                    <div class="card-body">
                                        Simply click on the "Sign Up" button at the top right corner. You can register
                                        as a User, Driver, or Service Center partner. Verification documents may be
                                        required for partners.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Renting -->
                    <div class="tab-pane fade" id="v-pills-rent" role="tabpanel">
                        <h4 class="font-weight-bold mb-4 text-dark">Car Rental</h4>
                        <div class="accordion" id="accordionRent">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapseR1">
                                            <div class="icon-box"><i class="fas fa-file-contract"></i></div>
                                            What documents do I need to rent a car?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseR1" class="collapse show" data-parent="#accordionRent">
                                    <div class="card-body">
                                        You need a valid Driving License, a government-issued ID proof
                                        (Aadhaar/Passport), and a credit/debit card for the security deposit.
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                            data-target="#collapseR2">
                                            <div class="icon-box"><i class="fas fa-gas-pump"></i></div>
                                            Is fuel included in the price?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseR2" class="collapse" data-parent="#accordionRent">
                                    <div class="card-body">
                                        No, fuel is not included. The car is provided with a full tank and should be
                                        returned with a full tank. Alternatively, you can pay for the missing fuel at
                                        current market rates plus a service charge.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Drivers -->
                    <div class="tab-pane fade" id="v-pills-drive" role="tabpanel">
                        <h4 class="font-weight-bold mb-4 text-dark">Professional Drivers</h4>
                        <div class="accordion" id="accordionDrive">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapseD1">
                                            <div class="icon-box"><i class="fas fa-user-check"></i></div>
                                            Are your drivers verified?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseD1" class="collapse show" data-parent="#accordionDrive">
                                    <div class="card-body">
                                        Yes, 100%. Every driver on CAR2GO undergoes a strict background check, license
                                        verification, and skill assessment before they can accept bookings.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Safety -->
                    <div class="tab-pane fade" id="v-pills-safe" role="tabpanel">
                        <h4 class="font-weight-bold mb-4 text-dark">Safety & Support</h4>
                        <div class="accordion" id="accordionSafe">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse"
                                            data-target="#collapseS1">
                                            <div class="icon-box"><i class="fas fa-life-ring"></i></div>
                                            What if the car breaks down?
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseS1" class="collapse show" data-parent="#accordionSafe">
                                    <div class="card-body">
                                        We offer 24/7 roadside assistance. In case of a breakdown, call our support
                                        hotline immediately. We will arrange for a replacement vehicle or repair
                                        support.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>