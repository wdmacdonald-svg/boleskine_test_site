document.addEventListener('DOMContentLoaded', () => {
    const logoContainer = document.getElementById('dynamic-logo-container');
    const logoTargetSlot = document.getElementById('logo-target-slot');
    const header = document.getElementById('main-header');

    const heroSection = document.getElementById('gallery-hero');

    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenuDrawer = document.getElementById('mobile-menu-drawer');
    const navLinks = document.querySelectorAll('#desktop-nav a, #mobile-menu-drawer a');

    function updateAnimations() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const heroHeight = heroSection ? heroSection.offsetHeight : 380;

        if (scrollTop > 80) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        const animationRange = 300;
        const progress = Math.min(scrollTop / animationRange, 1);

        if (progress === 1) {
            logoContainer.className = 'logo-docked';
            logoContainer.style.top = '';
            logoContainer.style.left = '';
            logoContainer.style.transform = '';
            logoContainer.style.width = '';
            logoContainer.style.height = '';

            if (logoContainer.parentNode !== logoTargetSlot) {
                logoTargetSlot.appendChild(logoContainer);
            }
        } else {
            if (logoContainer.parentNode !== document.body) {
                document.body.appendChild(logoContainer);
            }

            logoContainer.className = 'logo-center';

            const startWidth = 70;
            const startHeight = 70;
            const startTop = 1.5 * 16;
            const startLeft = 2 * 16;

            const slotRect = logoTargetSlot.getBoundingClientRect();
            const endWidth = 65;
            const endHeight = 65;
            const endTop = slotRect.top + (slotRect.height / 2) - endHeight / 2;
            const endLeft = slotRect.left;

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

    window.addEventListener('scroll', updateAnimations);
    window.addEventListener('resize', updateAnimations);

    setTimeout(updateAnimations, 100);

    const revealElements = document.querySelectorAll('.reveal-on-scroll');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));

    mobileMenuToggle.addEventListener('click', () => {
        const isOpen = mobileMenuDrawer.classList.toggle('open');
        mobileMenuToggle.classList.toggle('active');

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

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            mobileMenuDrawer.classList.remove('open');
            mobileMenuToggle.classList.remove('active');
            const bars = mobileMenuToggle.querySelectorAll('.bar');
            bars[0].style.transform = '';
            bars[1].style.opacity = '';
            bars[2].style.transform = '';

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
