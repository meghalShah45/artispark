// ============================================
// Mobile Menu Toggle
// ============================================

const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const navMenu = document.getElementById('navMenu');
const navLinks = document.querySelectorAll('.nav-link');

mobileMenuToggle?.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    mobileMenuToggle.classList.toggle('active');
});

// Close mobile menu when clicking a link
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        navMenu.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
    });
});

// ============================================
// Navbar Scroll Effect
// ============================================

const navbar = document.getElementById('navbar');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ============================================
// Smooth Scroll for Navigation Links
// ============================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 80; // Account for fixed navbar
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ============================================
// Masonry Grid Layout using Masonry.js
// ============================================

let masonryInstances = [];

function initMasonryGrid() {
    // Destroy existing instances
    masonryInstances.forEach(instance => {
        if (instance) instance.destroy();
    });
    masonryInstances = [];
    
    // Initialize Masonry for all grids
    const grids = document.querySelectorAll('.masonry-grid');
    
    grids.forEach((grid) => {
        // Wait a bit for layout to settle
        setTimeout(() => {
            const msnry = new Masonry(grid, {
                itemSelector: '.masonry-item',
                columnWidth: '.grid-sizer',
                percentPosition: true,
                gutter: 8, // 0.5rem = 8px
                fitWidth: false,
                horizontalOrder: true
            });
            
            masonryInstances.push(msnry);
            
            // Layout again after images load
            if (typeof imagesLoaded !== 'undefined') {
                imagesLoaded(grid).on('progress', function() {
                    msnry.layout();
                });
                
                imagesLoaded(grid).on('always', function() {
                    msnry.layout();
                });
            }
            
            // Force layout after a short delay to ensure proper distribution
            setTimeout(() => {
                msnry.layout();
            }, 300);
        }, 100);
    });
}

// Initialize masonry on load and DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Masonry !== 'undefined') {
        initMasonryGrid();
    }
});

window.addEventListener('load', () => {
    if (typeof Masonry !== 'undefined') {
        initMasonryGrid();
    }
});

// Recalculate on resize (with debounce)
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        // Reinitialize on resize to recalculate column widths
        initMasonryGrid();
    }, 250);
});

// ============================================
// Intersection Observer for Fade-in Animations
// ============================================

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe sections and cards
document.addEventListener('DOMContentLoaded', () => {
    const sections = document.querySelectorAll('section');
    const cards = document.querySelectorAll('.service-card, .gallery-card');
    
    sections.forEach(section => {
        observer.observe(section);
    });
    
    cards.forEach(card => {
        observer.observe(card);
    });
});

// ============================================
// Contact Form Handling
// ============================================

const contactForm = document.getElementById('contactForm');

contactForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(contactForm);
    const data = Object.fromEntries(formData);
    
    // Here you would typically send the data to a server
    // For now, we'll just show a success message
    console.log('Form submitted:', data);
    
    // Show success message (you can customize this)
    alert('Thank you for your message! We\'ll get back to you soon.');
    
    // Reset form
    contactForm.reset();
});

// ============================================
// Gallery Lightbox and Video Handling
// ============================================

const galleryItems = document.querySelectorAll('.gallery-item');

galleryItems.forEach(item => {
    item.addEventListener('click', () => {
        // Check if it's a video item
        const videoPlaceholder = item.querySelector('.video-placeholder');
        if (videoPlaceholder) {
            // Handle video click - you can replace this with actual video modal
            const caption = item.querySelector('.gallery-caption')?.textContent || 'Video';
            alert(`Video: ${caption}\n\nIn production, this would open a video player or modal.`);
            return;
        }
        
        // For images, you can implement a lightbox here
        const card = item.querySelector('.gallery-card');
        if (card) {
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = '';
            }, 200);
        }
    });
});

// ============================================
// Lazy Loading Images (if you add real images later)
// ============================================

if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            }
        });
    });
    
    // Observe all images with data-src attribute
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ============================================
// Active Navigation Link Highlighting
// ============================================

function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (window.scrollY >= sectionTop - 100) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
}

window.addEventListener('scroll', updateActiveNavLink);
updateActiveNavLink(); // Initial call

// ============================================
// Performance: Debounce function
// ============================================

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================
// Pebble Services Horizontal Scroll Interaction
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.pebble-services-container');
    if (container) {
        let isDown = false;
        let startX;
        let scrollLeft;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.classList.add('active');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.classList.remove('active');
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
            container.classList.remove('active');
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    }
});

// ============================================
// Paint Canvas Animation (optional)
// ============================================

const paintCanvas = document.getElementById('paintCanvas');
if (paintCanvas) {
    const ctx = paintCanvas.getContext('2d');
    paintCanvas.width = window.innerWidth;
    paintCanvas.height = window.innerHeight;
    
    let particles = [];
    const particleCount = 30;
    
    class Particle {
        constructor() {
            this.x = Math.random() * paintCanvas.width;
            this.y = Math.random() * paintCanvas.height;
            this.size = Math.random() * 3 + 1;
            this.speedX = Math.random() * 2 - 1;
            this.speedY = Math.random() * 2 - 1;
            this.color = Math.random() > 0.5 ? 'rgba(127, 179, 168, 0.3)' : 'rgba(212, 175, 55, 0.3)';
        }
        
        update() {
            this.x += this.speedX;
            this.y += this.speedY;
            
            if (this.x > paintCanvas.width) this.x = 0;
            if (this.x < 0) this.x = paintCanvas.width;
            if (this.y > paintCanvas.height) this.y = 0;
            if (this.y < 0) this.y = paintCanvas.height;
        }
        
        draw() {
            ctx.fillStyle = this.color;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }
    
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }
    
    function animate() {
        ctx.clearRect(0, 0, paintCanvas.width, paintCanvas.height);
        particles.forEach(particle => {
            particle.update();
            particle.draw();
        });
        requestAnimationFrame(animate);
    }
    
    animate();
    
    window.addEventListener('resize', () => {
        paintCanvas.width = window.innerWidth;
        paintCanvas.height = window.innerHeight;
    });
}

// ============================================
// Console Welcome Message
// ============================================

console.log('%c Welcome to ArtiSpark! ', 'background: #7FB3A8; color: #fff; font-size: 20px; padding: 10px;');
console.log('%c Create • Connect • Craft ', 'color: #D4AF37; font-size: 14px;');
