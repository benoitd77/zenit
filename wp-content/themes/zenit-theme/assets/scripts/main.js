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

    function timeConverter(UNIX_timestamp) {
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Janvier', 'Février', 'Mars', 'Avris', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var time = date + ' ' + month + ' ' + year;
        return time;
    }

    //function resizeInstagram() {
    //    $('.image-instagram').each(function(){
    //        $(this).height('auto');
    //    });
    //
    //    $('.image-instagram').each(function(){
    //        var currentWidth = $(this).width();
    //        $(this).height(currentWidth);
    //    });
    //}

    function configurer() {
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
            //$.ajax({
            //    type: "GET",
            //    dataType: "jsonp",
            //    url: "https://api.instagram.com/v1/users/13754559/media/recent/?access_token=13754559.1677ed0.df157d21b47d4606b91f0c3e73fa078b&count=8",
            //
            //    success: function(res) {
            //
            //        var contenuTexte = $('.instagram-quote').text();
            //        var contenuButton = $('.instagram-button').text();
            //
            //        var squareInfo = "<div class='col-lg-2 col-md-3 col-sm-4 col-xs-12 square-info'><div class='image-instagram texte'><h3>#hookeqc</h3><p>" + contenuTexte + "</p></div></div>";
            //
            //        for (i = 0; i < 8; i++) {
            //            var imgUrl = res.data[i].images.thumbnail.url;
            //
            //            if (imgUrl.indexOf('/s150x150') !== -1) {
            //                imgUrl = imgUrl.replace('/s150x150', '/s640x640');
            //            }
            //            var user = res.data[i].user.full_name;
            //
            //            var date = timeConverter(res.data[i].created_time);
            //
            //            var imageInta = "<div class=''><a target='_blank' class='image-cont' href='" + res.data[i].link + "'><div class='image-instagram' style='background-image:url(" + imgUrl + ");'><div class='content-background'><div class='content'><p>@" + user + "</p><p>" + date + "</p></div></div></div></a></div>";
            //
            //            $('.instagram').append(imageInta);
            //        }
            //
            //        $('.instagram').slick({
            //            dots: false,
            //            infinite: true,
            //            speed: 500,
            //            cssEase: 'ease-in-out',
            //            arrows: false,
            //            slidesToShow: 5,
            //            centerMode: true,
            //            responsive: [
            //                {
            //                    breakpoint: 1550,
            //                    settings: {
            //                        slidesToShow: 4
            //                    }
            //                },
            //                {
            //                    breakpoint: 1430,
            //                    settings: {
            //                        slidesToShow: 3
            //                    }
            //                },
            //                {
            //                    breakpoint: 780,
            //                    settings: {
            //                        slidesToShow: 2
            //                    }
            //                },
            //                {
            //                    breakpoint: 550,
            //                    settings: {
            //                        slidesToShow: 1
            //                    }
            //                }
            //
            //
            //            ]
            //        });
            //    }
            //
            //});

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

            //resizeInstagram();
            //
            //$(win).on('resize', function(e) {
            //    resizeInstagram();
            //});
        }

        $(win).on('scroll', function(e) {
            var div = $('body');
            var divfrfx = $('html');
            //alert( $(win).scrollTop());

            if ($(win).scrollTop() === 0) {

                $('.nav-primary').removeClass('fixed');
            } else {

                $('.nav-primary').addClass('fixed');
            }
        });

        // infobulle
        if ($('body').hasClass('single-product')) {
            $('#specs .title-meter').on('mouseenter', function() {
                var currentId = $(this).attr('data-id');
                $('#specs .description-meter[data-id-desc="' + currentId + '"').css('display', 'block').hide().fadeIn();
                //$('#specs .description-meter[data-id-desc="'+currentId+'"').fadeIn();
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

                    console.log(lat);
                    console.log(lng);


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

    }

    win.onload = configurer;

    // Load Events
    $(document).ready(UTIL.loadEvents);

})(window, jQuery);