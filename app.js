/**
 * Smart Journal - Main JavaScript Application
 */

class SmartJournal {
    constructor() {
        this.currentPage = 'dashboard';
        this.entries = [];
        this.user = null;
        this.moods = {
            happy: { emoji: '😊', label: 'Happy', color: '#fbbf24' },
            excited: { emoji: '🤩', label: 'Excited', color: '#f472b6' },
            calm: { emoji: '😌', label: 'Calm', color: '#34d399' },
            neutral: { emoji: '😐', label: 'Neutral', color: '#94a3b8' },
            sad: { emoji: '😢', label: 'Sad', color: '#60a5fa' },
            angry: { emoji: '😠', label: 'Angry', color: '#f87171' },
            anxious: { emoji: '😰', label: 'Anxious', color: '#a78bfa' },
            tired: { emoji: '😴', label: 'Tired', color: '#9ca3af' }
        };
        this.categories = {
            work: { label: 'Work', color: '#3b82f6', icon: '💼' },
            personal: { label: 'Personal', color: '#ec4899', icon: '👤' },
            ideas: { label: 'Ideas', color: '#f59e0b', icon: '💡' },
            health: { label: 'Health', color: '#10b981', icon: '❤️' },
            gratitude: { label: 'Gratitude', color: '#8b5cf6', icon: '🙏' }
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupModals();
        this.setupToasts();
        this.setupAnimations();
        this.loadTheme();
    }

    setupEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.entry-favorite-btn')) {
                this.toggleFavorite(e.target.closest('.entry-card'));
            }
            if (e.target.closest('.entry-archive-btn')) {
                this.archiveEntry(e.target.closest('.entry-card'));
            }
            if (e.target.closest('.entry-delete-btn')) {
                this.deleteEntry(e.target.closest('.entry-card'));
            }
            if (e.target.closest('.entry-restore-btn')) {
                this.restoreEntry(e.target.closest('.entry-card'));
            }
            if (e.target.closest('.entry-permanent-delete-btn')) {
                this.permanentDeleteEntry(e.target.closest('.entry-card'));
            }
        });
    }

    setupModals() {
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) this.closeModal(overlay.id);
            });
        });

        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => {
                const overlay = btn.closest('.modal-overlay');
                if (overlay) this.closeModal(overlay.id);
            });
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    setupToasts() {
        if (!document.getElementById('toastContainer')) {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
    }

    showToast(message, type = 'info', duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        toast.innerHTML = `
            <span>${icons[type]}</span>
            <span>${message}</span>
        `;
        
        container.appendChild(toast);
        
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    setupAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
    }

    animateNumber(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const isFloat = String(targetValue).includes('.');
        const target = parseFloat(targetValue);
        const duration = 1500;
        const start = 0;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);
            const current = start + (target - start) * easeOut;
            
            element.textContent = isFloat ? current.toFixed(1) : Math.floor(current);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    triggerConfetti(element) {
        const rect = element.getBoundingClientRect();
        const colors = ['#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#f472b6'];
        
        for (let i = 0; i < 30; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '9999';
            confetti.style.left = rect.left + rect.width / 2 + 'px';
            confetti.style.top = rect.top + 'px';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            
            document.body.appendChild(confetti);
            
            const angle = (Math.random() - 0.5) * Math.PI * 2;
            const velocity = Math.random() * 200 + 100;
            const vx = Math.cos(angle) * velocity;
            const vy = Math.sin(angle) * velocity - 200;
            
            let x = 0, y = 0;
            let rotation = 0;
            const rotationSpeed = (Math.random() - 0.5) * 720;
            
            const animate = () => {
                x += vx * 0.016;
                y += vy * 0.016 + 300 * 0.016 * 0.016;
                rotation += rotationSpeed * 0.016;
                
                confetti.style.transform = `translate(${x}px, ${y}px) rotate(${rotation}deg)`;
                confetti.style.opacity = 1 - y / 400;
                
                if (y < 400) {
                    requestAnimationFrame(animate);
                } else {
                    confetti.remove();
                }
            };
            
            requestAnimationFrame(animate);
        }
    }

    loadTheme() {
        const savedTheme = localStorage.getItem('theme');
        const btn = document.getElementById('themeToggle');
        const html = document.documentElement;

        if (savedTheme === 'light') {
            // Light mode
            html.removeAttribute('data-theme');
            html.classList.remove('dark');
            document.body.classList.add('light-mode');
            if (btn) btn.textContent = '☀️';
        } else {
            // Dark mode (default)
            html.setAttribute('data-theme', 'dark');
            html.classList.add('dark');
            document.body.classList.remove('light-mode');
            if (btn) btn.textContent = '🌙';
        }
    }
}

// Global theme toggle function
function toggleTheme() {
    const html = document.documentElement;
    const btn = document.getElementById('themeToggle');
    const isDark = html.getAttribute('data-theme') === 'dark' || html.classList.contains('dark');

    if (isDark) {
        // Switch to light
        html.removeAttribute('data-theme');
        html.classList.remove('dark');
        document.body.classList.add('light-mode');
        localStorage.setItem('theme', 'light');
        if (btn) btn.textContent = '☀️';
    } else {
        // Switch to dark
        html.setAttribute('data-theme', 'dark');
        html.classList.add('dark');
        document.body.classList.remove('light-mode');
        localStorage.setItem('theme', 'dark');
        if (btn) btn.textContent = '🌙';
    }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.app = new SmartJournal();
});

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.95); }
    }
`;
document.head.appendChild(style);