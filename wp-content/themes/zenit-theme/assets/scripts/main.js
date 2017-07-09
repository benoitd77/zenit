(function(win, $) {

    // Use this variable to set up the common and page specific functions. If you
    // rename this variable, you will also need to rename the namespace below.
    var Sage = {

    };

    // The routing fires all common scripts, followed by the page specific scripts.
    // Add additional events for more control over timing e.g. a finalize event
    var UTIL = {
        fire: function(func, funcname, args) {
            var fire;
            var namespace = Sage;
            funcname = (funcname === undefined) ? 'init' : funcname;
            fire = func !== '';
            fire = fire && namespace[func];
            fire = fire && typeof namespace[func][funcname] === 'function';

            if (fire) {
                namespace[func][funcname](args);
            }
        },
        loadEvents: function() {
            // Make height adjustments to the bottom sale section
            adjustColHeight();

            // Fire common init JS
            UTIL.fire('common');

            // Fire page-specific init JS, and then finalize JS
            $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
                UTIL.fire(classnm);
                UTIL.fire(classnm, 'finalize');
            });

            // Fire common finalize JS
            UTIL.fire('common', 'finalize');
        }
    };

    function adjustColHeight() {
        if ($('body').hasClass('single-product')) {
            var colRight = $('#single-prod');
            var colMenu  = $('#fixed-menu-single-product');

            if (colRight.length > 0 && colMenu.length > 0) {
                colMenu.css('height', colRight.height);
            }
        }
    }

    function timeConverter(UNIX_timestamp) {
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Janvier', 'Février', 'Mars', 'Avris', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var time = date + ' ' + month + ' ' + year;
        return time;
    }

    function configurer() {
        // Make height adjustments to the bottom sale section
        adjustColHeight();

        $(function() {
            // initialize skrollr if the window width is large enough
            if ($(win).width() > 768) {
                var s = skrollr.init({
                    forceHeight: false,
                    mobileDeceleration: 1,
                    mobileCheck: function mobileOff() {
                        //hack - forces mobile version to be off
                        //return true;
                    }
                });
            }
        });

        if ($('.cont-woocommerce-message').length > 0) {
            $('.cont-woocommerce-message .close-message').click(function() {
                $(this).parent().fadeOut();
            });
        }

        if ($(win).width() > 1024) {
            if ($('.elevate-zoom').length > 0) {
                $('.elevate-zoom').each(function() {
                    $(this).elevateZoom({
                        zoomType: "inner",
                        cursor: "crosshair"
                    });
                });
            }
        } else {
            if ($('.board').length > 0) {
                $('.board').click(function() {
                    $(this).find('.hover').css('opacity', '1');
                });
            }

            $(win).on('scroll', function() {
                $('.board').each(function() {
                    $(this).find('.hover').removeAttr('style');
                });
            });
        }

        $('#list-variations .variation').each(function() {
            var valSelect = $(this).attr('data-slug');
            var idSelect = $(this).attr('data-list');
            var current = $(this);

            if ($(".variations select option[value='" + valSelect + "']").length <= 0) {
                current.fadeOut();
            }

            current.click(function() {
                $('#' + idSelect).find('option').attr('selected', false);
                $('#' + idSelect + ' option[value="' + valSelect + '"]').attr('selected', true);
                var liste = $('#' + idSelect).html();
                $('#' + idSelect).change();
                $('#' + idSelect).html(liste);

                var parent = $(this).parent();

                parent.find('.variation.current').removeClass('current');

                $(this).addClass('current');
            });
        });

        $('#fixed-menu-single-product a').click(function() {
            var current = $(this);

            $('#fixed-menu-single-product .current').each(function() {
                $(this).removeClass('current');
            });

            setTimeout(function() {
                current.addClass('current');
            }, 850);
        });

        if ($('body').hasClass('single-product')) {
            $(win).on('scroll', function() {
                $('#fixed-menu-single-product .current').each(function() {
                    $(this).removeClass('current');
                });
            });
        }

        $('.slider-fullscreen').slick({
            dots: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            fade: true,
            speed: 1000,
            cssEase: 'linear',
            arrows: false,
            autoplay: true,
            lazyLoad: 'progressive',
            lazyLoadBuffer: 0
        });

        $('.slider-single').slick({
            dots: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
            fade: true,
            speed: 1500,
            cssEase: 'linear',
            arrows: false,
            autoplay: true,
            lazyLoad: 'progressive',
            lazyLoadBuffer: 0
        });

        $('#slider-container').addClass('visible-slider');

        //Animation des ancres
        $('a[href^="#"]').on('click touchend', function() {

            //Si l'ancre sert à autre chose (sous-menu produits)
            if ($(this).hasClass('not-anchor') || $(this).parent().hasClass('not-anchor') || $(this).parent().parent().parent().attr('id') === 'menu-gris' || $(this).parent().attr('id') === 'menu-item-99' || $(this).is('[data-id]') || $(this).is('[data-lat]')) {
                // Do nothing
            } else {
                //Va chercher l'élément
                var the_id = $(this).attr("href");
                //Anime la page
                $('html, body').animate({
                    scrollTop: $(the_id).offset().top - 80
                }, 'slow');

                //Évite le retour en haut d'un ancre.
                return false;
            }
        });

        $('#btMenu').on('click', function() {
            $('.nav-primary').toggleClass('visually-visible');
            $('body').toggleClass('no-scroll');
        });

        $('#social-fixed').removeClass('visually-hidden');

        if ($('body').hasClass('home')) {
            //Transition du slider d'accueil
            $('#splash .slider-fullscreen .slide:eq(0) .content').addClass('visible-text');

            // On before slide change
            $('#splash .slider-fullscreen').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                $('#splash .slider-fullscreen .slide:eq(' + currentSlide + ') .content').removeClass('visible-text');
            });

            //On after slide change
            $('#splash .slider-fullscreen ').on('afterChange', function(event, slick, currentSlide, nextSlide) {
                $('#splash .slider-fullscreen .slide:eq(' + currentSlide + ') .content').addClass('visible-text');
            });
        }

        $(win).on('scroll', function(e) {
            var div = $('body');
            var divfrfx = $('html');

            if ($(win).scrollTop() === 0) {
                $('.nav-primary').removeClass('fixed');
                $('body').removeClass('fixed-menu');
            } else {
                $('.nav-primary').addClass('fixed');
                $('body').addClass('fixed-menu');
            }
        });

        $(win).resize(function() {
            // Make height adjustments to the bottom sale section
            adjustColHeight();
        });

        // infobulle
        if ($('body').hasClass('single-product')) {
            $('#specs .title-meter').on('mouseenter', function() {
                var currentId = $(this).attr('data-id');
                $('#specs .description-meter[data-id-desc="' + currentId + '"').css('display', 'block').hide().fadeIn();
            });

            $('#specs .title-meter').on('mouseleave', function() {
                var currentId = $(this).attr('data-id');
                $('#specs .description-meter[data-id-desc="' + currentId + '"').fadeOut();
            });
        }

        if ($('body').hasClass('page-template-page-team')) {
            var roadAtlasStyles = [{
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#e9e9e9"
                }, {
                    "lightness": 17
                }]
            }, {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 17
                }]
            }, {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 29
                }, {
                    "weight": 0.2
                }]
            }, {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 18
                }]
            }, {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            }, {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f5f5f5"
                }, {
                    "lightness": 21
                }]
            }, {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#dedede"
                }, {
                    "lightness": 21
                }]
            }, {
                "elementType": "labels.text.stroke",
                "stylers": [{
                    "visibility": "on"
                }, {
                    "color": "#ffffff"
                }, {
                    "lightness": 16
                }]
            }, {
                "elementType": "labels.text.fill",
                "stylers": [{
                    "saturation": 36
                }, {
                    "color": "#333333"
                }, {
                    "lightness": 40
                }]
            }, {
                "elementType": "labels.icon",
                "stylers": [{
                    "visibility": "off"
                }]
            }, {
                "featureType": "transit",
                "elementType": "geometry",
                "stylers": [{
                    "color": "#f2f2f2"
                }, {
                    "lightness": 19
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.fill",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 20
                }]
            }, {
                "featureType": "administrative",
                "elementType": "geometry.stroke",
                "stylers": [{
                    "color": "#fefefe"
                }, {
                    "lightness": 17
                }, {
                    "weight": 1.2
                }]
            }];

            $('.fat-team-section').each(function() {
                var current = $(this);
                var currentId = current.attr('data-id');
                var lat = parseFloat(current.attr('data-lat'));
                var lng = parseFloat(current.attr('data-lng'));

                if (lat && lng) {

                    var myLatLng = {
                        lat: lat,
                        lng: lng
                    };

                    var mapOptions = {
                        zoom: 6,
                        center: myLatLng,
                        scrollwheel: false,
                        navigationControl: false,
                        mapTypeControl: false,
                        scaleControl: false,
                        draggable: false,
                        disableDefaultUI: true
                    };

                    var map = new google.maps.Map(document.getElementById("map-" + currentId), mapOptions);
                    var mapZenit = new google.maps.StyledMapType(roadAtlasStyles);
                    map.mapTypes.set('map-zenit', mapZenit);
                    map.setMapTypeId('map-zenit');

                    var marker = new google.maps.Marker({
                        position: myLatLng,
                        map: map
                    });
                }
            });

            $('.fat-team-section').each(function() {
                var height = $(this).height();
                $(this).css('height', height);
                $(this).addClass('hidden-section');

            });

            $('.team-member').on('click', function() {
                var currentId = $(this).attr('data-id');
                var open = false;

                if (!$('#' + currentId).hasClass('hidden-section')) {
                    open = true;
                }

                $('.fat-team-section').each(function() {
                    $(this).addClass('hidden-section');
                });

                if (open === false) {
                    $('#' + currentId).removeClass('hidden-section');
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $('#' + currentId).offset().top - 105
                        }, 500);
                    }, 500);
                }
            });

            $('.close-team').on('click', function() {
                $(this).parent().parent().parent().addClass('hidden-section');
                $('html, body').animate({
                    scrollTop: $(".row-team").offset().top - 105
                }, 500);
            });
        }

        $('#video-lightbox').on('click', function() {
            $('#video-lightbox').removeClass('open');
            setTimeout(function() {
                $('#video-lightbox .video').html('');
                $('body').removeClass('no-scroll');
                $('#video-lightbox').addClass('hidden-lightbox');
            }, 600);
        });

        //Au clique d'un trigger video
        $('.videoTrigger').click(function() {
            //Si c'est un vidéo
            $('#video-lightbox').removeClass('hidden-lightbox');

            setTimeout(function() {
                $('#video-lightbox').addClass('open');
            }, 500);

            var idVideo = $(this).attr('data-id');
            var debutString = "";
            var finString = "";

            if (idVideo.indexOf('youtube') >= 0) {
                debutString = '<iframe class="youtube" width="80%" height="400px" src="';
                finString = '?autoplay=1" frameborder="0" allowfullscreen></iframe>';
                idVideo = idVideo.replace("watch?v=", "v/");
            } else {
                debutString = '<iframe class="vimeo" src="//player.vimeo.com/video/';
                finString = '?autoplay=1&showinfo=0" width="80%" height="400px" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                idVideo = idVideo.replace('https://vimeo.com/', '');
                idVideo.trim();
            }

            $('#video-lightbox .video').html(debutString + idVideo + finString);
            $('body').addClass('no-scroll');

            var ratio = 0;
            if (idVideo.indexOf('youtube') >= 0) {
                ratio = 1.78;
            } else {
                ratio = 2.35;
            }

            var largeur = $('#video-lightbox iframe').width();
            $('#video-lightbox iframe').height(largeur / ratio);
        });

        //Close video lightbox, on click on close-button
        $('.close-videoPanel').click(function() {
            $('#video-lightbox').removeClass('open');
            setTimeout(function() {
                $('#video-lightbox .video').html('');
                $('body').removeClass('no-scroll');
                $('#video-lightbox').addClass('hidden-lightbox');
            }, 600);
        });

        function toggleMobileMenu(menu) {
            var submenu    = menu.next(),
                menuParent = menu.parent();

            if (menu.hasClass('opened')) {
                submenu.slideUp('fast');
                menu.removeClass('opened');
                menuParent.removeClass('opened');
            } else {
                submenu.slideDown('fast');
                menu.addClass('opened');
                menuParent.addClass('opened');
            }
        }

        $('.menu-second-level a').click(function (e) {
            toggleMobileMenu($(this));
        });

        $('.menu-third-level').click(function (e) {
            toggleMobileMenu($(this).find('>a'));
        });

        $('.menu-second-level').hover(
            function(){
                if ($(document).width() > 858) {
                    $(this).find('>.sub-menu').stop().fadeIn(500);
                }
            }, function() {
                if ($(document).width() > 858) {
                    $(this).find('>.sub-menu').stop().fadeOut(500);
                }
            }
        );
    }

    win.onload = configurer;

    // Load Events
    $(document).ready(UTIL.loadEvents);

})(window, jQuery);