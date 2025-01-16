class MoozSlideshow extends EventEmitter {
    constructor(element) {
        super();
        
        // Main wrapper
        this.DOM = {
            el: element
        };
        
        // The slides
        this.DOM.slides = [...this.DOM.el.querySelectorAll('.slide')];
        
        // Navigation controls
        this.DOM.nav = {
            el: this.DOM.el.querySelector('.slides-nav'),
            prev: this.DOM.el.querySelector('.slides-nav__button--prev'),
            next: this.DOM.el.querySelector('.slides-nav__button--next'),
            current: this.DOM.el.querySelector('.slides-nav__index-current'),
            total: this.DOM.el.querySelector('.slides-nav__index-total')
        };
        
        // Array of Slide objects
        this.slides = [];
        this.DOM.slides.forEach(slide => this.slides.push(new Slide(slide)));
        
        // Total number of slides
        this.slidesTotal = this.slides.length;
        
        // Current position
        this.current = 0;
        
        // Clip path settings
        this.config = {
            clipPath: {
                initial: 'circle(55% at 70% 50%)',
                final: 'circle(15% at 70% 50%)',
                hover: 'circle(20% at 30% 50%)'
            }
        };

        // Get navigation settings from data attributes
        this.navPosition = this.DOM.el.getAttribute('data-nav-position') || 'bottom-center';
        this.navShape = this.DOM.el.getAttribute('data-nav-shape') || 'arrow';
        this.navSize = this.DOM.el.getAttribute('data-nav-size') || '1';

        // Apply navigation settings
        this.updateNavigationStyle();

        // Get navigation colors from data attributes
        this.navColor = this.DOM.el.getAttribute('data-nav-color') || '#ffffff';
        this.navHoverColor = this.DOM.el.getAttribute('data-nav-hover-color') || '#cccccc';

        // Apply navigation colors
        this.updateNavigationColors();

        // Get slider height settings
        this.updateSliderHeight();
        
        // Listen for resize events
        window.addEventListener('resize', () => this.updateSliderHeight());

        this.init();
        this.initEvents();
    }

    init() {
        // Start with first slide as current
        this.slides[this.current].DOM.el.classList.add('slide--current');
        
        // Set initial clip path
        gsap.set(this.slides[this.current].DOM.imgWrap, {
            clipPath: this.config.clipPath.initial
        });

        // Add hover animations for each slide
        this.slides.forEach(slide => {
            slide.DOM.link.addEventListener('mouseenter', () => {
                gsap.killTweensOf(slide.DOM.imgWrap);
                gsap.to(slide.DOM.imgWrap, {
                    duration: 1,
                    ease: 'expo',
                    clipPath: this.config.clipPath.hover
                });
            });

            slide.DOM.link.addEventListener('mouseleave', () => {
                gsap.killTweensOf(slide.DOM.imgWrap);
                gsap.to(slide.DOM.imgWrap, {
                    duration: 1,
                    ease: 'expo',
                    clipPath: this.config.clipPath.initial
                });
            });
        });

        // Update navigation total
        if (this.DOM.nav.total) {
            this.DOM.nav.total.innerHTML = this.slidesTotal < 10 ? `0${this.slidesTotal}` : this.slidesTotal;
        }
        
        // Update current slide number
        this.updateCurrentSlideNumber();
    }

    initEvents() {
        // Add click handlers for navigation buttons
        if (this.DOM.nav.prev) {
            this.DOM.nav.prev.addEventListener('click', () => this.prev());
        }
        if (this.DOM.nav.next) {
            this.DOM.nav.next.addEventListener('click', () => this.next());
        }

        // Listen for updateCurrent events
        this.on('updateCurrent', () => this.updateCurrentSlideNumber());
    }

    updateCurrentSlideNumber() {
        if (this.DOM.nav.current) {
            this.DOM.nav.current.innerHTML = `<span>${this.current + 1 < 10 ? `0${this.current + 1}` : this.current + 1}</span>`;
        }
    }

    next() {
        this.navigate('next');
    }

    prev() {
        this.navigate('prev');
    }

    navigate(direction) {
        if (this.isAnimating) return;
        this.isAnimating = true;

        const currentSlide = this.slides[this.current];
        this.current = direction === 'next' ? 
            this.current < this.slidesTotal - 1 ? this.current + 1 : 0 :
            this.current > 0 ? this.current - 1 : this.slidesTotal - 1;
        const upcomingSlide = this.slides[this.current];

        gsap.timeline({
            onStart: () => {
                upcomingSlide.DOM.el.classList.add('slide--current');
            },
            onComplete: () => {
                this.isAnimating = false;
                currentSlide.DOM.el.classList.remove('slide--current');
            }
        })
        .addLabel('start', 0)
        // Установка начальных позиций для нового слайда
        .set(upcomingSlide.DOM.imgWrap, {
            y: direction === 'next' ? '100%' : '-100%',
            clipPath: this.config.clipPath.final
        }, 'start')
        .set(upcomingSlide.DOM.el, {
            opacity: 1
        }, 'start')
        .set(upcomingSlide.DOM.img, {
            y: direction === 'next' ? '-50%' : '50%'
        }, 'start')
        .set(upcomingSlide.DOM.text, {
            y: direction === 'next' ? '100%' : '-100%'
        }, 'start')
        .set(upcomingSlide.DOM.link, {
            opacity: 0
        }, 'start')
        // Анимация текущего слайда
        .to(currentSlide.DOM.imgWrap, {
            duration: 1,
            ease: 'power3',
            clipPath: this.config.clipPath.final,
            rotation: 0.001
        }, 'start')
        .to(currentSlide.DOM.text, {
            duration: 1,
            ease: 'power3',
            y: direction === 'next' ? '-100%' : '100%'
        }, 'start')
        .to(currentSlide.DOM.link, {
            duration: 0.5,
            ease: 'power3',
            opacity: 0
        }, 'start')
        // Движение текущего слайда
        .to(currentSlide.DOM.imgWrap, {
            duration: 1,
            ease: 'power2.inOut',
            y: direction === 'next' ? '-100%' : '100%',
            rotation: 0.001
        }, 'start+=0.6')
        .to(currentSlide.DOM.img, {
            duration: 1,
            ease: 'power2.inOut',
            y: direction === 'next' ? '50%' : '-50%'
        }, 'start+=0.6')
        // Движение нового слайда
        .to(upcomingSlide.DOM.imgWrap, {
            duration: 1,
            ease: 'power2.inOut',
            y: '0%',
            rotation: 0.001
        }, 'start+=0.6')
        .to(upcomingSlide.DOM.img, {
            duration: 1,
            ease: 'power2.inOut',
            y: '0%'
        }, 'start+=0.6')
        // Анимация маски нового слайда
        .to(upcomingSlide.DOM.imgWrap, {
            duration: 1.5,
            ease: 'expo.inOut',
            clipPath: this.config.clipPath.initial
        }, 'start+=1.2')
        // Анимация текста нового слайда
        .to(upcomingSlide.DOM.text, {
            duration: 1.5,
            ease: 'expo.inOut',
            y: '0%',
            rotation: 0.001,
            stagger: direction === 'next' ? 0.1 : -0.1
        }, 'start+=1.1')
        // Анимация ссылки нового слайда
        .to(upcomingSlide.DOM.link, {
            duration: 1,
            ease: 'expo.in',
            opacity: 1
        }, 'start+=1.4');

        this.emit('updateCurrent', this.current);
    }

    updateNavigationStyle() {
        if (this.DOM.nav.el) {
            // Remove existing position and shape classes
            this.DOM.nav.el.className = 'slides-nav';
            
            // Add position class
            this.DOM.nav.el.classList.add(`slides-nav--${this.navPosition}`);
            
            // Add shape class
            this.DOM.nav.el.classList.add(`slides-nav--${this.navShape}`);
            
            // Set size
            this.DOM.nav.el.setAttribute('data-nav-size', this.navSize);
        }
    }

    updateNavigationColors() {
        if (this.DOM.nav.el) {
            // Set initial colors using CSS Custom Properties
            this.DOM.nav.el.style.setProperty('--nav-color', this.navColor);
            this.DOM.nav.el.style.setProperty('--nav-hover-color', this.navHoverColor);
            
            // Add color transition class
            this.DOM.nav.el.classList.add('nav-color-transition');
        }
    }

    updateSliderHeight() {
        const height = getComputedStyle(this.DOM.el).height;
        document.documentElement.style.setProperty('--slider-height', height);
        document.documentElement.style.setProperty('--slider-negative-height', `-${height}`);
    }
}

class Slide {
    constructor(el) {
        this.DOM = {
            el: el
        };
        this.DOM.imgWrap = this.DOM.el.querySelector('.slide__img-wrap');
        this.DOM.img = this.DOM.imgWrap.querySelector('.slide__img');
        this.DOM.headline = this.DOM.el.querySelector('.slides__caption-headline');
        this.DOM.text = this.DOM.headline.querySelectorAll('.text-row > span');
        this.DOM.link = this.DOM.el.querySelector('.slides__caption-link');
    }
}

// Initialize slideshows
document.addEventListener('DOMContentLoaded', () => {
    const slideshows = document.querySelectorAll('.slideshow');
    slideshows.forEach(slideshow => {
        const instance = new MoozSlideshow(slideshow);
        window.initMoozSlideshowExtensions(instance);
    });
    
    // Добавим обработку header
    const headers = document.querySelectorAll('.mooz-slideshow-header');
    headers.forEach(header => {
        const transparency = parseFloat(header.dataset.transparency) || 0;
        
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > 50;
            header.classList.toggle('scrolled', scrolled);
            
            if (scrolled) {
                header.style.background = `rgba(0, 0, 0, ${0.8 - transparency})`;
            } else {
                header.style.background = `rgba(0, 0, 0, ${transparency})`;
            }
        });
    });
});

// Support for Elementor frontend
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.hooks.addAction('frontend/element_ready/mooz_slideshow.default', ($element) => {
        const slideshow = $element.find('.slideshow')[0];
        if (slideshow) {
            const instance = new MoozSlideshow(slideshow);
            window.initMoozSlideshowExtensions(instance);
        }
    });
}); 