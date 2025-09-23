<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Footer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gis-footer {
            background: linear-gradient(135deg, #b71c1c, #d32f2f);
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
        }
        
        .footer-section h5 {
            color: #ffcdd2;
            border-left: 3px solid #ffcdd2;
            padding-left: 10px;
            margin-bottom: 20px;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ffebee;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .contact-info {
            color: #ffebee;
        }
        
        .contact-info i {
            width: 20px;
            margin-right: 10px;
            color: #ffcdd2;
        }
        
        .social-links a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            transition: all 0.3s;
            color: white;
        }
        
        .social-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #ffcdd2;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .quick-stats {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        
        .stat-label {
            font-size: 12px;
            color: #ffcdd2;
        }
    </style>
</head>
<body>
    <!-- Footer Section -->
    <footer class="gis-footer">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-md-4 footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-globe-americas"></i> Laguna GIS
                    </div>
                    <p class="contact-info">
                        The Geographic Information System for Laguna Province, Philippines. 
                        Providing spatial data analysis, mapping solutions, and regional planning tools.
                    </p>
                    <div class="social-links mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-md-2 footer-section">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="{{ route('welcome') }}">Home</a></li>
                        <li><a href="{{ route('shapefiles.index') }}">Shapefiles</a></li>
                        <li><a href="{{ route('legends.index') }}">Legends</a></li>
                        <li><a href="{{ route('analysis.flood-areas') }}">Flood Analysis</a></li>
                        <li><a href="{{ route('analysis.health-status') }}">Health Status</a></li>
                    </ul>
                </div>
                
                <!-- Resources -->
                <div class="col-md-3 footer-section">
                    <h5>Resources</h5>
                    <ul class="footer-links">
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">API Reference</a></li>
                        <li><a href="#">Data Sources</a></li>
                        <li><a href="#">Tutorials</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="col-md-3 footer-section">
                    <h5>Contact Us</h5>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> Provincial Capitol, Santa Cruz, Laguna</p>
                        <p><i class="fas fa-phone"></i> (049) 123-4567</p>
                        <p><i class="fas fa-envelope"></i> gis@laguna.gov.ph</p>
                        <p><i class="fas fa-clock"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="quick-stats">
                        <div class="row">
                            <div class="col-4 stat-item">
                                <div class="stat-number">42</div>
                                <div class="stat-label">Layers</div>
                            </div>
                            <div class="col-4 stat-item">
                                <div class="stat-number">15</div>
                                <div class="stat-label">Maps</div>
                            </div>
                            <div class="col-4 stat-item">
                                <div class="stat-number">7</div>
                                <div class="stat-label">Analyses</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-md-start">
                        <p>&copy; 2025 Laguna Province GIS. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>Province of Laguna, Philippines</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>