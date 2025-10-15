<!-- resources/views/layouts/footer.blade.php -->
<footer class="gis-footer">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6 footer-section">
                <div class="footer-logo">
                    <i class="fas fa-globe-americas"></i> Laguna GIS
                </div>
                <p class="footer-description">
                    The official Geographic Information System for Laguna Province, Philippines. 
                    Providing comprehensive spatial data analysis, mapping solutions, and regional planning tools for sustainable development.
                </p>
                <div class="social-links">
                    <a href="#" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 footer-section">
                <h5>Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('welcome') }}"><i class="fas fa-home fa-sm me-2"></i>Home</a></li>
                    <li><a href=""><i class="fas fa-palette fa-sm me-2"></i>Flood Areas</a></li>
                    <li><a href=""><i class="fas fa-water fa-sm me-2"></i>Health Status</a></li>
                    <li><a href=""><i class="fas fa-heartbeat fa-sm me-2"></i>Land Use</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div class="col-lg-3 col-md-6 footer-section">
                <h5>Resources</h5>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-book fa-sm me-2"></i>Documentation</a></li>
                    <li><a href="#"><i class="fas fa-code fa-sm me-2"></i>API Reference</a></li>
                    <li><a href="#"><i class="fas fa-database fa-sm me-2"></i>Data Sources</a></li>
                    <li><a href="#"><i class="fas fa-graduation-cap fa-sm me-2"></i>Tutorials</a></li>
                    <li><a href="#"><i class="fas fa-question-circle fa-sm me-2"></i>FAQ & Help</a></li>
                    <li><a href="#"><i class="fas fa-newspaper fa-sm me-2"></i>News & Updates</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 footer-section">
                <h5>Contact Information</h5>
                <div class="contact-info">
                    <p>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Provincial Capitol Building, Santa Cruz, Laguna 4001, Philippines</span>
                    </p>
                    <p>
                        <i class="fas fa-phone"></i>
                        <span>(049) 123-4567</span>
                    </p>
                    <p>
                        <i class="fas fa-envelope"></i>
                        <span>gis@laguna.gov.ph</span>
                    </p>
                    <p>
                        <i class="fas fa-clock"></i>
                        <span>Monday - Friday: 8:00 AM - 5:00 PM</span>
                    </p>
                    <p>
                        <i class="fas fa-user-tie"></i>
                        <span>Provincial Planning and Development Office</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-lg-6 text-lg-start text-center mb-2 mb-lg-0">
                    <p>&copy; 2025 Laguna Province GIS. All rights reserved. | Version 2.1.0</p>
                </div>
                <div class="col-lg-6 text-lg-end text-center">
                    <p>
                        <i class="fas fa-heart text-danger"></i> 
                        Proudly serving the Province of Laguna, Philippines
                        <i class="fas fa-flag text-warning ms-2"></i>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .gis-footer {
        background: linear-gradient(135deg, var(--dark-red), var(--primary-red));
        color: white;
        padding: 40px 0 20px;
        margin-top: auto;
    }
    
    .footer-section h5 {
        color: #ffcdd2;
        border-left: 3px solid var(--accent-red);
        padding-left: 12px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
    }
    
    .footer-links li {
        margin-bottom: 8px;
    }
    
    .footer-links a {
        color: #ffebee;
        text-decoration: none;
        transition: all 0.3s ease;
        display: block;
        padding: 4px 0;
    }
    
    .footer-links a:hover {
        color: white;
        padding-left: 8px;
        transform: translateX(5px);
    }
    
    .contact-info {
        color: #ffebee;
    }
    
    .contact-info p {
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
    }
    
    .contact-info i {
        width: 20px;
        margin-right: 12px;
        color: var(--accent-red);
        margin-top: 2px;
    }
    
    .social-links {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
        color: white;
        text-decoration: none;
    }
    
    .social-links a:hover {
        background-color: var(--accent-red);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding-top: 20px;
        margin-top: 40px;
        text-align: center;
        color: #ffcdd2;
        font-size: 0.9rem;
    }
    
    .footer-logo {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: white;
    }
    
    .footer-logo i {
        color: var(--accent-red);
        margin-right: 10px;
    }
    
    .quick-stats {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.1));
        border-radius: 12px;
        padding: 20px;
        margin-top: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    
    .stat-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: white;
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: #ffcdd2;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .footer-description {
        color: #ffebee;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    @media (max-width: 768px) {
        .gis-footer {
            padding: 30px 0 15px;
        }
        
        .footer-section {
            margin-bottom: 30px;
        }
        
        .footer-section h5 {
            font-size: 1rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            text-align: center;
        }
        
        .social-links {
            justify-content: center;
        }
        
        .quick-stats {
            margin-top: 20px;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        statNumbers.forEach(stat => {
            const target = parseInt(stat.textContent);
            let current = 0;
            const increment = target / 30;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    stat.textContent = target;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(current);
                }
            }, 50);
        });
    });
</script>