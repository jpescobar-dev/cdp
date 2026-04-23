var App = function () {
    var MediaSize = {
        xl: 1200,
        lg: 992,
        md: 991,
        sm: 576
    };

    var ToggleClasses = {
        headerhamburger: '.toggle-sidebar',
        inputFocused: 'input-focused',
    };

    var Selector = {
        mainHeader: '.header.navbar',
        headerhamburger: '.toggle-sidebar',
        fixed: '.fixed-top',
        mainContainer: '.main-container',
        sidebar: '#sidebar',
        sidebarContent: '#sidebar-content',
        sidebarStickyContent: '.sticky-sidebar-content',
        ariaExpandedTrue: '#sidebar [aria-expanded="true"]',
        ariaExpandedFalse: '#sidebar [aria-expanded="false"]',
        contentWrapper: '#content',
        contentWrapperContent: '.container',
        mainContentArea: '.main-content',
        searchFull: '.toggle-search',
        overlay: {
            sidebar: '.overlay',
            cs: '.cs-overlay',
            search: '.search-overlay'
        }
    };

    var topbarPs = null;
    var submenuPs = null;

    function elementExists(selector) {
        return $(selector).length > 0;
    }

    function destroyPerfectScrollbar(instance) {
        if (instance && typeof instance.destroy === 'function') {
            instance.destroy();
        }
        return null;
    }

    var toggleFunction = {
        sidebar: function () {
            $('.sidebarCollapse').off('click.appSidebar').on('click.appSidebar', function (e) {
                e.preventDefault();

                if (!elementExists(Selector.mainContainer)) {
                    return;
                }

                $(Selector.mainContainer).toggleClass('topbar-closed');
                $(Selector.mainContainer).toggleClass('sbar-open');
                $(Selector.overlay.sidebar).toggleClass('show');
                $('html, body').toggleClass('sidebar-noneoverflow');
            });
        },

        overlay: function () {
            $('#dismiss, .overlay').off('click.appOverlay').on('click.appOverlay', function () {
                if (elementExists(Selector.mainContainer)) {
                    $(Selector.mainContainer).removeClass('topbar-closed sbar-open');
                }

                $(Selector.overlay.sidebar).removeClass('show');
                $('html, body').removeClass('sidebar-noneoverflow');
            });
        },

        deactivateScroll: function () {
            topbarPs = destroyPerfectScrollbar(topbarPs);
        },

        search: function () {
            $(Selector.searchFull).off('click.appSearch').on('click.appSearch', function () {
                var $searchAnimated = $(this).parents('.search-animated');

                if (!$searchAnimated.length) {
                    return;
                }

                $searchAnimated.find('.search-full').addClass(ToggleClasses.inputFocused);
                $searchAnimated.addClass('show-search');
                $(Selector.overlay.search).addClass('show');
            });

            $(Selector.overlay.search).off('click.appSearchOverlay').on('click.appSearchOverlay', function () {
                $(this).removeClass('show');

                var $searchAnimated = $(Selector.searchFull).parents('.search-animated');
                $searchAnimated.find('.search-full').removeClass(ToggleClasses.inputFocused);
                $searchAnimated.removeClass('show-search');
            });
        }
    };

    var mobileFunctions = {
        activateScroll: function () {
            if (!elementExists('#topbar') || typeof PerfectScrollbar === 'undefined') {
                return;
            }

            topbarPs = destroyPerfectScrollbar(topbarPs);
            topbarPs = new PerfectScrollbar('#topbar', {
                wheelSpeed: 0.5,
                swipeEasing: true,
                minScrollbarLength: 40,
                maxScrollbarLength: 300
            });
        }
    };

    var desktopFunctions = {
        activateScroll: function () {
            if (!elementExists('.menu-categories li.menu .submenu') || typeof PerfectScrollbar === 'undefined') {
                return;
            }

            submenuPs = destroyPerfectScrollbar(submenuPs);
            submenuPs = new PerfectScrollbar('.menu-categories li.menu .submenu', {
                wheelSpeed: 0.5,
                swipeEasing: true,
                minScrollbarLength: 40,
                maxScrollbarLength: 300
            });
        },

        preventAccordionOnClick: function () {
            $('.menu > a[data-toggle="collapse"], .menu.single-menu a[data-toggle="collapse"]')
                .off('click.appAccordion')
                .on('click.appAccordion', function (e) {
                    var getWindowWidth = window.innerWidth;
                    if (getWindowWidth > 991) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
        }
    };

    var inBuiltfunctionality = {
        preventScrollBody: function () {
            if (!elementExists('#topbar')) {
                return;
            }

            $('#topbar').off('mousewheel.appScroll DOMMouseScroll.appScroll')
                .on('mousewheel.appScroll DOMMouseScroll.appScroll', function (e) {
                    var scrollTo = null;

                    if (e.type === 'mousewheel' && e.originalEvent) {
                        scrollTo = (e.originalEvent.wheelDelta * -1);
                    } else if (e.type === 'DOMMouseScroll' && e.originalEvent) {
                        scrollTo = 40 * e.originalEvent.detail;
                    }

                    if (scrollTo !== null) {
                        e.preventDefault();
                        $(this).scrollTop(scrollTo + $(this).scrollTop());
                    }
                });
        },

        default: function () {
            if (!elementExists('.main-content') || !elementExists('.sidenav')) {
                return;
            }

            $(document).off('scroll.appDefault').on('scroll.appDefault', function () {
                var elementMainContent = $('.main-content');
                var sideNav = $('.sidenav');

                if (!elementMainContent.length || !sideNav.length) {
                    return;
                }

                var mainOffset = elementMainContent.offset();
                if (!mainOffset) {
                    return;
                }

                var elementOffset = mainOffset.top;
                var windowScroll = $(window).scrollTop();

                if (windowScroll >= elementOffset) {
                    sideNav.css('top', '42px');
                } else {
                    sideNav.css('top', '147px');
                }
            });
        },

        languageDropdown: function () {
            var dropdownItems = document.querySelectorAll('.more-dropdown .dropdown-item');
            var dropdownImage = document.querySelector('.more-dropdown .dropdown-toggle > img');

            if (!dropdownItems.length || !dropdownImage) {
                return;
            }

            for (var i = 0; i < dropdownItems.length; i++) {
                dropdownItems[i].addEventListener('click', function () {
                    var imgValue = this.getAttribute('data-img-value');
                    if (!imgValue) {
                        return;
                    }

                    dropdownImage.setAttribute('src', '/assets/img/' + imgValue + '.png');
                });
            }
        }
    };

    var _mobileResolution = {
        onRefresh: function () {
            var windowWidth = window.innerWidth;
            if (windowWidth <= MediaSize.md) {
                toggleFunction.search();
                mobileFunctions.activateScroll();
            }
        },

        onResize: function () {
            $(window).off('resize.appMobile').on('resize.appMobile', function (e) {
                e.preventDefault();

                var windowWidth = window.innerWidth;
                if (windowWidth <= MediaSize.md) {
                    toggleFunction.search();
                    mobileFunctions.activateScroll();
                }
            });
        }
    };

    var _desktopResolution = {
        onRefresh: function () {
            var windowWidth = window.innerWidth;
            if (windowWidth > MediaSize.md) {
                toggleFunction.search();
                desktopFunctions.preventAccordionOnClick();
                desktopFunctions.activateScroll();
            }
        },

        onResize: function () {
            $(window).off('resize.appDesktop').on('resize.appDesktop', function (e) {
                e.preventDefault();

                var windowWidth = window.innerWidth;
                if (windowWidth > MediaSize.md) {
                    toggleFunction.search();
                    toggleFunction.deactivateScroll();
                    desktopFunctions.activateScroll();
                }
            });
        }
    };

    return {
        init: function () {
            toggleFunction.sidebar();
            toggleFunction.overlay();

            _desktopResolution.onRefresh();
            _desktopResolution.onResize();

            _mobileResolution.onRefresh();
            _mobileResolution.onResize();

            if (!$('body').hasClass('alt-menu')) {
                inBuiltfunctionality.default();
            }

            inBuiltfunctionality.languageDropdown();
            inBuiltfunctionality.preventScrollBody();
        },
    };
}();