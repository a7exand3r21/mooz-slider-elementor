<?php
class Mooz_Slideshow_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'mooz_slideshow';
    }

    public function get_title() {
        return __('Mooz Slideshow', 'mooz-slideshow');
    }

    public function get_icon() {
        return 'eicon-slideshow';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_script_depends() {
        return ['gsap', 'mooz-slideshow'];
    }

    public function get_style_depends() {
        return ['mooz-slideshow'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Slides', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'slide_image',
            [
                'label' => __('Choose Image', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'headline_text',
            [
                'label' => __('Headline', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Slide Headline', 'mooz-slideshow'),
            ]
        );

        $repeater->add_control(
            'link_text',
            [
                'label' => __('Link Text', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Explore', 'mooz-slideshow'),
            ]
        );

        $repeater->add_control(
            'link_url',
            [
                'label' => __('Link URL', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'mooz-slideshow'),
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __('Slides', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'headline_text' => __('First Slide', 'mooz-slideshow'),
                        'link_text' => __('Explore', 'mooz-slideshow'),
                    ]
                ],
                'title_field' => '{{{ headline_text }}}',
            ]
        );

        $this->add_responsive_control(
            'slider_height',
            [
                'label' => __('Slider Height', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 2000,
                        'step' => 10,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'vh',
                    'size' => 80,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slideshow' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.has-overlay-header' => 'margin-top: -{{SIZE}}{{UNIT}}; padding-top: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_responsive_control(
            'slider_min_height',
            [
                'label' => __('Minimum Height', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 1000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slideshow' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Controls
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'headline_color',
            [
                'label' => __('Headline Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slides__caption-headline' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Секция анимации
        $this->start_controls_section(
            'animation_section',
            [
                'label' => __('Animation', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'animation_type',
            [
                'label' => __('Animation Type', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'circle',
                'options' => [
                    'circle' => __('Circle', 'mooz-slideshow'),
                    'square' => __('Square', 'mooz-slideshow'),
                    'diamond' => __('Diamond', 'mooz-slideshow'),
                ],
            ]
        );

        $this->add_control(
            'animation_duration',
            [
                'label' => __('Animation Duration', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 1,
                ],
            ]
        );

        $this->add_control(
            'mask_color',
            [
                'label' => __('Mask Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'transparent',
            ]
        );

        $this->end_controls_section();

        // Секция навигации
        $this->start_controls_section(
            'navigation_section',
            [
                'label' => __('Navigation', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'nav_position',
            [
                'label' => __('Navigation Position', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'bottom-center',
                'options' => [
                    'bottom-center' => __('Bottom Center', 'mooz-slideshow'),
                    'bottom-right' => __('Bottom Right', 'mooz-slideshow'),
                    'bottom-left' => __('Bottom Left', 'mooz-slideshow'),
                    'center-right' => __('Center Right', 'mooz-slideshow'),
                    'center-left' => __('Center Left', 'mooz-slideshow'),
                ],
            ]
        );

        $this->add_control(
            'nav_size',
            [
                'label' => __('Navigation Size', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.5,
                        'max' => 2,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 1,
                ],
            ]
        );

        $this->add_control(
            'nav_shape',
            [
                'label' => __('Navigation Shape', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'arrow',
                'options' => [
                    'arrow' => __('Arrow', 'mooz-slideshow'),
                    'circle' => __('Circle', 'mooz-slideshow'),
                ],
            ]
        );

        $this->add_control(
            'nav_color',
            [
                'label' => __('Navigation Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .slides-nav__button svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav--circle .slides-nav__button' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav--circle .slides-nav__button::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav__index' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'nav_hover_color',
            [
                'label' => __('Navigation Hover Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .slides-nav__button:hover svg' => 'fill: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav--circle .slides-nav__button:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav--circle .slides-nav__button:hover::before' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slides-nav__index:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Секция автопрокрутки
        $this->start_controls_section(
            'autoplay_section',
            [
                'label' => __('Autoplay', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Enable Autoplay', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 5000,
                'min' => 1000,
                'max' => 20000,
                'step' => 500,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // В методе register_controls() добавим новую секцию:
        $this->start_controls_section(
            'header_section',
            [
                'label' => __('Header Overlay', 'mooz-slideshow'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_header',
            [
                'label' => __('Show Header Overlay', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => '',
            ]
        );

        $this->add_control(
            'menu_location',
            [
                'label' => __('Menu Location', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_menu_locations(),
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'header_transparency',
            [
                'label' => __('Header Transparency', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'menu_position',
            [
                'label' => __('Menu Position', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'left' => __('Left', 'mooz-slideshow'),
                    'center' => __('Center', 'mooz-slideshow'),
                    'right' => __('Right', 'mooz-slideshow'),
                ],
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'menu_hover_effect',
            [
                'label' => __('Menu Hover Effect', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'fade',
                'options' => [
                    'none' => __('None', 'mooz-slideshow'),
                    'fade' => __('Fade', 'mooz-slideshow'),
                    'underline' => __('Underline', 'mooz-slideshow'),
                    'overline' => __('Overline', 'mooz-slideshow'),
                ],
                'condition' => [
                    'show_header' => 'yes',
                ],
                'prefix_class' => 'menu-hover-',
            ]
        );

        $this->add_control(
            'menu_link_color',
            [
                'label' => __('Menu Link Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .mooz-slideshow-header .menu a' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'menu_link_hover_color',
            [
                'label' => __('Menu Link Hover Color', 'mooz-slideshow'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#cccccc',
                'selectors' => [
                    '{{WRAPPER}} .mooz-slideshow-header .menu a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .menu-hover-underline .menu a::after' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .menu-hover-overline .menu a::before' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    // Добавим метод для получения зарегистрированных меню
    private function get_menu_locations() {
        $locations = get_nav_menu_locations();
        $options = ['' => __('Select Menu', 'mooz-slideshow')];
        
        foreach ($locations as $location => $menu_id) {
            $menu = wp_get_nav_menu_object($menu_id);
            if ($menu) {
                $options[$location] = $menu->name;
            }
        }
        
        return $options;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Добавляем класс если включен header
        $classes = ['slideshow'];
        if ($settings['show_header'] === 'yes') {
            $classes[] = 'has-overlay-header';
        }
        
        // Выводим header если включен
        if ($settings['show_header'] === 'yes') {
            ?>
            <header class="mooz-slideshow-header menu-position-<?php echo esc_attr($settings['menu_position']); ?>" 
                    data-transparency="<?php echo esc_attr($settings['header_transparency']['size']); ?>">
                <div class="menu-container">
                    <?php
                    if ($settings['menu_location']) {
                        wp_nav_menu([
                            'theme_location' => $settings['menu_location'],
                            'container' => false,
                            'menu_class' => 'menu menu-hover-' . $settings['menu_hover_effect'],
                            'menu_id' => 'mooz-header-menu',
                        ]);
                    }
                    ?>
                </div>
            </header>
            <?php
        }
        
        // Добавляем data-атрибуты для настроек
        $this->add_render_attribute('slideshow', [
            'class' => $classes,
            'data-animation-type' => $settings['animation_type'],
            'data-animation-duration' => $settings['animation_duration']['size'],
            'data-mask-color' => $settings['mask_color'],
            'data-nav-position' => $settings['nav_position'],
            'data-nav-size' => $settings['nav_size']['size'],
            'data-nav-shape' => $settings['nav_shape'],
            'data-autoplay' => $settings['autoplay'],
            'data-autoplay-delay' => $settings['autoplay_delay'],
            'data-nav-color' => $settings['nav_color'],
            'data-nav-hover-color' => $settings['nav_hover_color'],
        ]);
        
        ?>
        <div <?php echo $this->get_render_attribute_string('slideshow'); ?>>
            <?php foreach ($settings['slides'] as $index => $slide) : ?>
                <figure class="slide <?php echo $index === 0 ? 'slide--current' : ''; ?>">
                    <div class="slide__img-wrap">
                        <div class="slide__img" style="background-image: url(<?php echo esc_url($slide['slide_image']['url']); ?>)"></div>
                    </div>
                    <figcaption class="slide__caption">
                        <h2 class="slides__caption-headline">
                            <?php 
                            $headline = wp_kses_post($slide['headline_text']);
                            $words = explode(' ', $headline);
                            foreach ($words as $word) {
                                echo '<div class="text-row"><span>' . $word . '</span></div>';
                            }
                            ?>
                        </h2>
                        <a class="slides__caption-link" href="<?php echo esc_url($slide['link_url']['url']); ?>">
                            <span><?php echo esc_html($slide['link_text']); ?></span>
                        </a>
                    </figcaption>
                </figure>
            <?php endforeach; ?>

            <nav class="slides-nav">
                <button class="slides-nav__button slides-nav__button--prev" aria-label="Previous slide">
                    <?php if ($settings['nav_shape'] === 'arrow') : ?>
                        <svg><path d="M1.176 11.532a.5.5 0 00-.352.936c5.228 1.96 9.475 5.555 12.752 10.797a.5.5 0 00.848-.53c-3.39-5.424-7.81-9.163-13.248-11.203z"></path><path d="M1.176 12.468a.5.5 0 01-.352-.936C6.052 9.572 10.3 5.978 13.576.735a.5.5 0 01.848.53c-3.39 5.424-7.81 9.163-13.248 11.203z"></path><path d="M1 12.5a.5.5 0 110-1h53a.5.5 0 110 1H1z"></path></svg>
                    <?php endif; ?>
                </button>
                <button class="slides-nav__button slides-nav__button--next" aria-label="Next slide">
                    <?php if ($settings['nav_shape'] === 'arrow') : ?>
                        <svg><path d="M53.824 11.532a.5.5 0 01.352.936c-5.228 1.96-9.475 5.555-12.752 10.797a.5.5 0 01-.848-.53c3.39-5.424 7.81-9.163 13.248-11.203z"></path><path d="M53.824 12.468a.5.5 0 00.352-.936C48.948 9.572 44.7 5.978 41.424.735a.5.5 0 00-.848.53c3.39 5.424 7.81 9.163 13.248 11.203z"></path><path d="M54 12.5a.5.5 0 100-1H1a.5.5 0 100 1h53z"></path></svg>
                    <?php endif; ?>
                </button>
                <div class="slides-nav__index">
                    <span class="slides-nav__index-current"><span>1</span></span>
                    &mdash;
                    <span class="slides-nav__index-total"></span>
                </div>
            </nav>
        </div>
        <?php
    }
} 