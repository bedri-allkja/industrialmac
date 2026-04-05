/**
 * Front theme: backgrounds, carousels, and storefront widgets.
 */
(function ($) {
    'use strict';

    function applyDataBackgrounds() {
        $('[data-background]').each(function () {
            var url = $(this).attr('data-background');
            if (url) {
                $(this).css('background-image', 'url("' + url.replace(/"/g, '\\"') + '")');
            }
        });
    }

    function closeMobileMenu() {
        $('.mobile-menu').removeClass('active');
        $('.overlay').removeClass('active');
        $('body').removeClass('menu-open');
    }

    $(function () {
        applyDataBackgrounds();

        $(document).on('click', '.mobile-menu-toggle', function (e) {
            e.preventDefault();
            $('#searchBar').removeClass('active');
            $('body').removeClass('search-open');
            $('.mobile-menu').addClass('active');
            $('.overlay').addClass('active');
            $('body').addClass('menu-open');
        });

        $(document).on('click', '.mobile-menu .close', function (e) {
            e.preventDefault();
            closeMobileMenu();
        });

        $(document).on('click', '.overlay', function () {
            closeMobileMenu();
            $('#searchBar').removeClass('active');
            $('body').removeClass('search-open');
        });

        $(document).on('click', '#searchIcon', function (e) {
            e.preventDefault();
            closeMobileMenu();
            $('#searchBar').toggleClass('active');
            $('body').toggleClass('search-open', $('#searchBar').hasClass('active'));
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
                $('#searchBar').removeClass('active');
                $('body').removeClass('search-open');
            }
        });

        if ($.fn.slick && $('.hero-slider-wrapper').length) {
            $('.hero-slider-wrapper').slick({
                dots: true,
                arrows: true,
                infinite: true,
                adaptiveHeight: true,
                autoplay: true,
                autoplaySpeed: 5000,
            });
        }

        var cateSliderSel = '.home-cate-slider, .home3-cate-slider';
        if ($.fn.slick && $(cateSliderSel).length) {
            $(cateSliderSel).each(function () {
                if ($(this).hasClass('slick-initialized')) {
                    return;
                }
                $(this).slick({
                    dots: false,
                    arrows: true,
                    infinite: false,
                    slidesToShow: 6,
                    slidesToScroll: 2,
                    responsive: [
                        { breakpoint: 1200, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                        { breakpoint: 992, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                        { breakpoint: 768, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                        { breakpoint: 576, settings: { slidesToShow: 2, slidesToScroll: 1 } },
                    ],
                });
            });
        }

        if ($.fn.slick && $('.product-cards-slider').length) {
            $('.product-cards-slider').each(function () {
                var $el = $(this);
                if ($el.hasClass('slick-initialized')) {
                    return;
                }
                $el.slick({
                    dots: false,
                    arrows: true,
                    infinite: false,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    responsive: [
                        { breakpoint: 1200, settings: { slidesToShow: 3 } },
                        { breakpoint: 992, settings: { slidesToShow: 2 } },
                        { breakpoint: 576, settings: { slidesToShow: 1 } },
                    ],
                });
            });
        }

        if ($.fn.niceSelect) {
            $('select').niceSelect();
        }
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    });
})(jQuery);
