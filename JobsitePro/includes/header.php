<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($page_title) ? $page_title : getSetting('site_description', 'Find your dream job in Getafe, Bohol'); ?>">
    <title><?php echo isset($page_title) ? $page_title : getSetting('site_name', 'Getafe Jobsite'); ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/admin.css">

    <!-- Fallback Inline CSS (if external files fail to load) -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-logo a {
            color: white;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 25px;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #e0e0e0;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
        }

        .btn-block {
            width: 100%;
        }

        .btn-large {
            padding: 14px 40px;
            font-size: 16px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 100px 40px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: center;
        }

        .hero-content h1 {
            color: white;
            font-size: 42px;
            margin-bottom: 15px;
        }

        .hero-content p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        /* Footer */
        .footer {
            background-color: #1a1a1a;
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .footer-section h4 {
            margin-bottom: 15px;
            color: #fff;
        }

        .footer-section p,
        .footer-section a {
            color: #ccc;
            font-size: 14px;
            text-decoration: none;
        }

        .footer-section a:hover {
            color: #007bff;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 20px;
            padding-right: 20px;
        }

        /* Job Cards */
        .job-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            margin-bottom: 20px;
        }

        .job-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
            border-color: #007bff;
        }

        .job-header {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: flex-start;
        }

        .job-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .tag {
            background-color: #f0f0f0;
            color: #666;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* No Results */
        .no-results {
            background-color: #f8f9fa;
            border: 2px dashed #ddd;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        /* Auth Box */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 200px);
            padding: 20px;
        }

        .auth-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
        }

        .auth-box h2 {
            margin-bottom: 10px;
            color: #007bff;
            text-align: center;
            font-size: 28px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .jobs-section {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .hero-content h1 {
                font-size: 28px;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .hero-buttons .btn {
                width: 100%;
            }
        }
    </style>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/favicon.png">
</head>
<body><!-- Application Features -->
<script src="<?php echo BASE_URL; ?>js/application-rich-editor.js"></script>
<script src="<?php echo BASE_URL; ?>js/application-notifications.js"></script></body>