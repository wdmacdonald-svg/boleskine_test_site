document.addEventListener('DOMContentLoaded', () => {
    // --- Select Elements ---
    const logoContainer = document.getElementById('dynamic-logo-container');
    const logoTargetSlot = document.getElementById('logo-target-slot');
    const header = document.getElementById('main-header');

    const heroSection = document.getElementById('hero');
    const slides = document.querySelectorAll('.hero-slide');

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuDrawer = document.getElementById('mobile-menu-drawer');
    const navLinks = document.querySelectorAll('#desktop-nav a, #mobile-menu-drawer a');

    // --- Configuration ---
    let autoSlideshowTimer;
    let currentSlideIndex = 0;
    let isIntroPlaying = true; // Auto slideshow plays until user scrolls
    const slideDuration = 7000; // Time per slide in ms

    // --- 1. Cinematic Slideshow Logic ---

    function showSlide(index) {
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
        currentSlideIndex = index;
    }

    function startAutoSlideshow() {
        if (autoSlideshowTimer) clearInterval(autoSlideshowTimer);
        autoSlideshowTimer = setInterval(() => {
            if (isIntroPlaying) {
                let nextIndex = (currentSlideIndex + 1) % slides.length;
                showSlide(nextIndex);
            }
        }, slideDuration);
    }

    // Initialize auto slideshow on page load
    if (slides.length > 0) {
        showSlide(0);
        startAutoSlideshow();
    }

    // --- 2. Dynamic Logo and Scroll Animations ---

    function updateAnimations() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const heroHeight = heroSection ? heroSection.offsetHeight : 0;

        // Disable auto-slideshow once user scrolls down significantly
        if (scrollTop > 50) {
            isIntroPlaying = false;
        } else {
            isIntroPlaying = true;
        }

        // A. Scroll-controlled slide progression
        // If the user is scrolling through the hero, let scroll control which slide is active
        if (heroSection && slides.length > 0 && scrollTop < heroHeight) {
            const scrollPercent = scrollTop / heroHeight;
            if (scrollPercent < 0.2) {
                showSlide(0); // Bowed
            } else if (scrollPercent >= 0.2 && scrollPercent < 0.45) {
                showSlide(1); // Standing
            } else if (scrollPercent >= 0.45 && scrollPercent < 0.7) {
                showSlide(2); // Determined
            } else {
                showSlide(3); // Hopeful
            }
        }

        // B. Sticky Navbar visual change
        if (scrollTop > 80) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // C. Dynamic Logo Position Interpolation
        // We interpolate position between centered (landing) and docked in navbar (scrolled)
        // Transition completes within the first 300px of scrolling
        const animationRange = 300;
        const progress = Math.min(scrollTop / animationRange, 1); // 0 to 1

        if (progress === 1) {
            // Fully Docked inside the navbar
            logoContainer.className = 'logo-docked';
            logoContainer.style.top = '';
            logoContainer.style.left = '';
            logoContainer.style.transform = '';
            logoContainer.style.width = '';
            logoContainer.style.height = '';

            // Move container into the slot element so it sits in document flow
            if (logoContainer.parentNode !== logoTargetSlot) {
                logoTargetSlot.appendChild(logoContainer);
            }
        } else {
            // In transition: make it fixed and interpolate coordinates
            if (logoContainer.parentNode !== document.body) {
                document.body.appendChild(logoContainer);
            }

            logoContainer.className = 'logo-center';

            // Starting parameters (Top-left near header)
            const startWidth = 70;
            const startHeight = 70;
            const startTop = 1.5 * 16; // 1.5rem in px
            const startLeft = 2 * 16;  // 2rem in px

            // Ending parameters (Navbar target slot position)
            const slotRect = logoTargetSlot.getBoundingClientRect();
            const endWidth = 65;
            const endHeight = 65;
            const endTop = slotRect.top + (slotRect.height / 2) - endHeight / 2;
            const endLeft = slotRect.left;

            // Interpolated values
            const currentWidth = startWidth + (endWidth - startWidth) * progress;
            const currentHeight = startHeight + (endHeight - startHeight) * progress;
            const currentTop = startTop + (endTop - startTop) * progress;
            const currentLeft = startLeft + (endLeft - startLeft) * progress;

            logoContainer.style.position = 'fixed';
            logoContainer.style.width = `${currentWidth}px`;
            logoContainer.style.height = `${currentHeight}px`;
            logoContainer.style.top = `${currentTop}px`;
            logoContainer.style.left = `${currentLeft}px`;
            logoContainer.style.transform = 'none';
        }
    }

    // Add scroll event listener
    window.addEventListener('scroll', updateAnimations);
    window.addEventListener('resize', updateAnimations);

    // Run once on load to establish correct states
    setTimeout(updateAnimations, 100);

    // --- 3. Scroll Reveal Elements (Intersection Observer) ---
    const revealElements = document.querySelectorAll('.reveal-on-scroll');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Animates only once
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px' // triggers slightly before entering viewport
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // --- 4. Mobile Navigation Drawer ---
    mobileMenuToggle.addEventListener('click', () => {
        const isOpen = mobileMenuDrawer.classList.toggle('open');
        mobileMenuToggle.classList.toggle('active');

        // Animate hamburger menu bars
        const bars = mobileMenuToggle.querySelectorAll('.bar');
        if (isOpen) {
            bars[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
            bars[1].style.opacity = '0';
            bars[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
        } else {
            bars[0].style.transform = '';
            bars[1].style.opacity = '';
            bars[2].style.transform = '';
        }
    });

    // Close drawer on clicking a navigation link
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            mobileMenuDrawer.classList.remove('open');
            mobileMenuToggle.classList.remove('active');
            const bars = mobileMenuToggle.querySelectorAll('.bar');
            bars[0].style.transform = '';
            bars[1].style.opacity = '';
            bars[2].style.transform = '';

            // Set active class on desktop nav links
            const href = link.getAttribute('href');
            document.querySelectorAll('#desktop-nav a').forEach(navA => {
                if (navA.getAttribute('href') === href) {
                    navA.classList.add('active');
                } else {
                    navA.classList.remove('active');
                }
            });
        });
    });
});
