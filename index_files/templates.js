const siteHeaderHTML = `
    <!-- Sticky Navigation Bar (Revealed fully as user scrolls) -->
    <header id="main-header">
        <div class="header-container">
            <!-- Target slot for the dynamic logo -->
            <div id="logo-target-slot">
                <div id="dynamic-logo-container" class="logo-docked">
                    <div class="logo-wrapper">
                        <img src="./index_files/logo.png" alt="Boleskine Camanachd Club Logo" id="dynamic-logo">
                        <div class="logo-shimmer"></div>
                    </div>
                </div>
            </div>

            <nav id="desktop-nav">
                <ul>
                    <li><a href="index.html#hero" class="active">Home</a></li>
                    <li><a href="about.html">About the Site</a></li>
                    <li><a href="https://boleskinecamanachdclub.com/test/heritage/">Heritage Site</a></li>
                    <li><a href="https://boleskinecamanachdclub.com/test/heritage/club-news/">Latest News</a></li>
                    <li><a href="contact.html" target="_blank">Contact</a></li>
                </ul>
            </nav>

            <div class="nav-actions">
                <button id="mobile-menu-toggle" aria-label="Toggle Menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu-drawer">
        <nav>
            <ul>
                <li><a href="index.html#hero">Home</a></li>
                <li><a href="about.html">About the Site</a></li>
                <li><a href="https://boleskinecamanachdclub.com/test/heritage/">Heritage Site</a></li>
                <li><a href="https://boleskinecamanachdclub.com/test/heritage/club-news/">Latest News</a></li>
                <li><a href="contact.html" target="_blank">Contact</a></li>
            </ul>
        </nav>
    </div>
`;

const siteFooterHTML = `
    <!-- Footer -->
    <footer id="contact">
        <div class="container footer-grid">
            <div class="footer-col footer-info">
                <img src="./index_files/logo.png" alt="Boleskine Camanachd Club Logo" class="footer-logo">
                <h3 class="font-cinzel text-gold mt-2">Boleskine Camanachd Club</h3>
                <p class="text-gray mt-2">Strathnairn, Foyers and Stratherrick's official shinty club, honoring the
                    sport of the Highlands since 1927</p>
                <div class="social-icons mt-3 mb-2">
                    <a href="https://www.facebook.com/people/Boleskine-Camanachd-Club/100057269995639/" target="_blank"
                        rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.youtube.com/results?search_query=shinty" target="_blank" rel="noopener"
                        aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4 class="font-cinzel text-gold">Quick Links</h4>
                <ul>
                    <li><a href="index.html#hero">Home</a></li>
                    <li><a href="index.html#heritage">Heritage &amp; History</a></li>
                    <li><a href="index.html#fixtures">Fixtures &amp; News</a></li>
                    <li><a href="contact.html" target="_blank">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="font-cinzel text-gold">Training &amp; Match Ground</h4>
                <p class="text-gray">Smith Park</p>
                <p class="text-gray">Inverarnie, Inverness-shire</p>
                <p class="text-gray">IV2 6XJ, Scotland</p>
                <p class="text-gold mt-3"><i class="fa-solid fa-envelope"></i> Email: info@boleskinecamanachdclub.com</p>
            </div>
        </div>

        <div class="footer-bottom text-center">
            <div class="container">
                <p class="text-gray">© 2026 Boleskine Camanachd Club. All rights reserved.</p>
                <p class="dev-credits mt-1">Designed by Trial and Error.</p>
            </div>
        </div>
    </footer>
`;

// Inject into placeholders immediately on script execution
document.addEventListener('DOMContentLoaded', () => {
    const headerSlot = document.getElementById('site-header');
    if (headerSlot) {
        headerSlot.innerHTML = siteHeaderHTML;
    }

    const footerSlot = document.getElementById('site-footer');
    if (footerSlot) {
        footerSlot.innerHTML = siteFooterHTML;
    }

    // Dynamically set the active navigation link based on current page
    const currentPath = window.location.pathname.split('/').pop() || 'index.html';
    const currentHash = window.location.hash;
    const navLinks = document.querySelectorAll('#desktop-nav a, #mobile-menu-drawer a');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        
        const linkHref = link.getAttribute('href');
        const [linkPath, linkHash] = linkHref.split('#');
        const path = linkPath || 'index.html';
        const hash = linkHash ? '#' + linkHash : '';
        
        // If on index.html, only highlight the exact matching hash (or Home if no hash is present)
        if (currentPath === 'index.html' || currentPath === '') {
            if (path === 'index.html' && (hash === currentHash || (hash === '#hero' && !currentHash))) {
                link.classList.add('active');
            }
        } 
        // If on another page (e.g. gallery), highlight based purely on path
        else {
            if (path === currentPath) {
                link.classList.add('active');
            }
        }
    });
});
