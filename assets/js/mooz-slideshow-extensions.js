class MoozSlideshowExtensions {
    constructor(slideshow) {
        this.slideshow = slideshow;
        this.autoplayTimer = null;
        this.settings = {};
        
        // Получаем настройки из data-атрибутов
        this.getSettings();
        this.init();
    }

    getSettings() {
        const element = this.slideshow.DOM.el;
        this.settings = {
            // Анимация
            animationType: element.dataset.animationType || 'circle',
            animationDuration: parseFloat(element.dataset.animationDuration) || 1,
            animationEase: element.dataset.animationEase || 'expo',
            
            // Маска
            maskColor: element.dataset.maskColor || 'transparent',
            
            // Автопрокрутка
            autoplay: element.dataset.autoplay === 'yes',
            autoplayDelay: parseFloat(element.dataset.autoplayDelay) || 5000,
            
            // Навигация
            navPosition: element.dataset.navPosition || 'bottom-center',
            navSize: parseFloat(element.dataset.navSize) || 1,
            navShape: element.dataset.navShape || 'arrow'
        };
    }

    init() {
        this.updateClipPathConfig();
        this.updateNavigation();
        this.initAutoplay();
    }

    updateClipPathConfig() {
        // Обновляем конфигурацию clip-path на основе настроек
        const shapes = {
            circle: {
                initial: 'circle(55% at 70% 50%)',
                final: 'circle(15% at 70% 50%)',
                hover: 'circle(20% at 30% 50%)'
            },
            square: {
                initial: 'inset(0% 0% 0% 0%)',
                final: 'inset(40% 40% 40% 40%)',
                hover: 'inset(35% 35% 35% 35%)'
            },
            diamond: {
                initial: 'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)',
                final: 'polygon(50% 25%, 75% 50%, 50% 75%, 25% 50%)',
                hover: 'polygon(50% 20%, 80% 50%, 50% 80%, 20% 50%)'
            }
        };

        this.slideshow.config.clipPath = shapes[this.settings.animationType] || shapes.circle;
        
        // Применяем цвет маски
        if (this.settings.maskColor !== 'transparent') {
            this.slideshow.slides.forEach(slide => {
                slide.DOM.imgWrap.style.backgroundColor = this.settings.maskColor;
            });
        }
    }

    updateNavigation() {
        const nav = this.slideshow.DOM.nav.el;
        if (!nav) return;

        // Позиционирование навигации
        const positions = {
            'bottom-center': { bottom: '2rem', left: '50%', transform: 'translateX(-50%)' },
            'bottom-right': { bottom: '2rem', right: '2rem' },
            'bottom-left': { bottom: '2rem', left: '2rem' },
            'center-right': { top: '50%', right: '2rem', transform: 'translateY(-50%)' },
            'center-left': { top: '50%', left: '2rem', transform: 'translateY(-50%)' }
        };

        Object.assign(nav.style, positions[this.settings.navPosition]);

        // Размер кнопок
        const scale = this.settings.navSize;
        const buttons = nav.querySelectorAll('.slides-nav__button');
        buttons.forEach(button => {
            button.style.transform = `scale(${scale})`;
        });

        // Форма кнопок
        if (this.settings.navShape === 'circle') {
            buttons.forEach(button => {
                button.style.borderRadius = '50%';
                button.style.padding = '1rem';
            });
        }
    }

    initAutoplay() {
        if (!this.settings.autoplay) return;

        let isPlaying = false;
        let autoplayTimer = null;

        const startAutoplay = () => {
            if (!isPlaying) {
                isPlaying = true;
                autoplayTimer = setInterval(() => {
                    if (isPlaying) {
                        this.slideshow.next();
                    }
                }, this.settings.autoplayDelay);
            }
        };

        const stopAutoplay = () => {
            isPlaying = false;
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        };

        // Простые обработчики для основного контейнера
        this.slideshow.DOM.el.addEventListener('mouseenter', () => {
            stopAutoplay();
        });

        this.slideshow.DOM.el.addEventListener('mouseleave', () => {
            startAutoplay();
        });

        // Запускаем автопрокрутку сразу
        startAutoplay();

        // Очистка при уничтожении
        this.cleanup = () => {
            stopAutoplay();
            this.slideshow.DOM.el.removeEventListener('mouseenter', stopAutoplay);
            this.slideshow.DOM.el.removeEventListener('mouseleave', startAutoplay);
        };
    }
}

// Инициализация расширений
window.initMoozSlideshowExtensions = (slideshow) => {
    return new MoozSlideshowExtensions(slideshow);
}; 