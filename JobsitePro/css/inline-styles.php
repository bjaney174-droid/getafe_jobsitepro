<?php
header('Content-type: text/css; charset: UTF-8');
?>

/* ========== CRITICAL STYLES ========== */

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

/* ========== NAVBAR ========== */
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
    display: flex;
    align-items: center;
    letter-spacing: -0.5px;
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
    padding-bottom: 5px;
    border-bottom: 2px solid transparent;
}

.nav-link:hover {
    color: #e0e0e0;
    border-bottom-color: #e0e0e0;
}

/* ========== BUTTONS ========== */
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
    letter-spacing: 0.5px;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.btn-primary:hover {
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
    transform: translateY(-2px);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    transform: translateY(-2px);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.btn-danger:hover {
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    transform: translateY(-2px);
}

.btn-block {
    width: 100%;
    display: block;
}

.btn-large {
    padding: 14px 40px;
    font-size: 16px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

/* ========== HERO SECTION ========== */
.hero-section {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    padding: 100px 40px;
    border-radius: 12px;
    margin: 30px 0;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 123, 255, 0.2);
}

.hero-content h1 {
    color: white;
    font-size: 42px;
    margin-bottom: 15px;
    font-weight: 700;
    letter-spacing: -1px;
}

.hero-content p {
    font-size: 18px;
    margin-bottom: 30px;
    opacity: 0.95;
    font-weight: 500;
}

.hero-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

/* ========== FORMS ========== */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
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
    transition: all 0.3s ease;
    background-color: #fff;
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

/* ========== ALERTS ========== */
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

/* ========== QUICK STATS ========== */
.quick-stats {
    background: white;
    padding: 40px 0;
    margin: 40px 0;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 30px;
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 10px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

/* ========== JOBS SECTION ========== */
.jobs-section {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
    margin: 30px 0;
}

.jobs-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ========== JOB CARDS ========== */
.job-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
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

.company-logo {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.company-logo-placeholder {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    flex-shrink: 0;
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
    font-weight: 500;
}

.job-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

/* ========== FOOTER ========== */
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
    font-size: 16px;
}

.footer-section p,
.footer-section a {
    color: #ccc;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s;
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
    padding: 0 20px;
}

/* ========== NO RESULTS ========== */
.no-results {
    background-color: #f8f9fa;
    border: 2px dashed #ddd;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    color: #666;
}

/* ========== AUTH BOX ========== */
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
    border: 1px solid #f0f0f0;
}

.auth-box h2 {
    margin-bottom: 10px;
    color: #007bff;
    text-align: center;
    font-size: 28px;
}

/* ========== RESPONSIVE ========== */
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

@media (max-width: 480px) {
    .container {
        padding: 0 10px;
    }
    
    .auth-box {
        padding: 20px;
    }
}