document.addEventListener('DOMContentLoaded', () => {
    // Particle Animation System
    const canvas = document.getElementById('particle-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let particles = [];
        const particleCount = 100;
        const colors = ['#1a3a5c', '#f5a623']; // Navy and Gold

        canvas.width = window.innerWidth;
        canvas.height = document.querySelector('.hero-section').offsetHeight; // Cover hero section

        function Particle(x, y, radius, color, velocity) {
            this.x = x;
            this.y = y;
            this.radius = radius;
            this.color = color;
            this.velocity = velocity;
        }

        Particle.prototype.draw = function() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2, false);
            ctx.fillStyle = this.color;
            ctx.fill();
        };

        Particle.prototype.update = function() {
            this.x += this.velocity.x;
            this.y += this.velocity.y;

            if (this.x + this.radius > canvas.width || this.x - this.radius < 0) {
                this.velocity.x = -this.velocity.x;
            }
            if (this.y + this.radius > canvas.height || this.y - this.radius < 0) {
                this.velocity.y = -this.velocity.y;
            }

            this.draw();
        };

        function initParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                const radius = Math.random() * 2 + 1;
                const x = Math.random() * (canvas.width - radius * 2) + radius;
                const y = Math.random() * (canvas.height - radius * 2) + radius;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const velocity = {
                    x: (Math.random() - 0.5) * 0.5,
                    y: (Math.random() - 0.5) * 0.5
                };
                particles.push(new Particle(x, y, radius, color, velocity));
            }
        }

        function animateParticles() {
            requestAnimationFrame(animateParticles);
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < particles.length; i++) {
                particles[i].update();

                // Draw lines between close particles
                for (let j = i; j < particles.length; j++) {
                    const p1 = particles[i];
                    const p2 = particles[j];
                    const distance = Math.sqrt((p1.x - p2.x)**2 + (p1.y - p2.y)**2);

                    if (distance < 100) { // Connect particles within 100px
                        ctx.beginPath();
                        ctx.moveTo(p1.x, p1.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(26, 58, 92, ${1 - (distance / 100)})`; // Navy with fading opacity
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
        }

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = document.querySelector('.hero-section').offsetHeight;
            initParticles();
        });

        initParticles();
        animateParticles();
    }

    // Typing Effect for Hero Heading
    const heroHeading = document.querySelector('.hero-section h1');
    const originalText = "Your Campus. One Hub.";
    let i = 0;
    let isDeleting = false;
    let typingSpeed = 150; // milliseconds

    function typeWriter() {
        if (!heroHeading) return;

        const currentText = originalText.substring(0, i);
        heroHeading.textContent = currentText;

        if (!isDeleting && i < originalText.length) {
            i++;
            typingSpeed = 150;
        } else if (isDeleting && i > 0) {
            i--;
            typingSpeed = 75;
        } else if (!isDeleting && i === originalText.length) {
            // Pause at the end of typing
            isDeleting = true;
            typingSpeed = 2000; // Longer pause before deleting
        } else if (isDeleting && i === 0) {
            isDeleting = false;
            typingSpeed = 500; // Pause before re-typing
        }

        setTimeout(typeWriter, typingSpeed);
    }

    // Replace the static text with an empty span for the typing effect
    const heroLead = document.querySelector('.hero-section p.lead');
    if (heroLead) {
        heroLead.innerHTML = `<span id="typing-text"></span>`;
        const typingTextElement = document.getElementById('typing-text');
        const typingEffectText = "Your Campus. One Hub.";
        let charIndex = 0;

        function type() {
            if (charIndex < typingEffectText.length) {
                typingTextElement.textContent += typingEffectText.charAt(charIndex);
                charIndex++;
                setTimeout(type, 100); // Typing speed
            }
        }
        type();
    }


    // Animated Counting Numbers in Stats Bar
    const statsNumbers = document.querySelectorAll('.card-text h3');
    const animateCount = (element, target) => {
        let count = 0;
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // ~60fps

        const updateCount = () => {
            count += increment;
            if (count < target) {
                element.textContent = Math.floor(count) + '+';
                requestAnimationFrame(updateCount);
            } else {
                element.textContent = target + '+';
            }
        };
        updateCount();
    };

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.5 // Trigger when 50% of the item is visible
    };

    const sectionObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Apply fade-in to sections
    document.querySelectorAll('.row:not(.hero-section .row), .mt-5.mb-3').forEach(section => {
        section.classList.add('fade-in-section');
        sectionObserver.observe(section);
    });

    // Observe stats numbers for counting animation
    const statsObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.textContent.replace('+', ''));
                animateCount(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    statsNumbers.forEach(num => {
        statsObserver.observe(num);
    });

    // Floating Animated Icons in Hero Section
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        const icons = [
            'fas fa-graduation-cap',
            'fas fa-book',
            'fas fa-newspaper'
        ];

        icons.forEach(iconClass => {
            const iconElement = document.createElement('i');
            iconElement.className = `${iconClass} floating-icon`;
            iconElement.style.left = `${Math.random() * 100}%`;
            iconElement.style.top = `${Math.random() * 100}%`;
            iconElement.style.animationDelay = `${Math.random() * 5}s`;
            heroSection.appendChild(iconElement);
        });
    }
});