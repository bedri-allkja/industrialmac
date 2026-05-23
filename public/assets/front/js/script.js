$(document).ready(function () {
  /*
  1. DATA BACKGROUND SET
  2. MOBILE MENU
  3. STICKY HEADER
  4. SEARCH BAR 
  5. NICE SELECT
  6. WOW JS 
  7. HIDE & SHOW PASSWORD 
  8. COUNTDOWN STARTS
  9. HERO SECTION SLIDER
  10. HOME CATE SLIDER
  11. PRODUCT DETAILS SLIDER
  12. PRICE RANGE SLIDER
  13. ADD DYNAMIC CLASS FOR SVG
  14. TOGGLE VENDOR DASHBOARD SIDEBAR
  15. DATA TABLE
  16. PRODUCT CARDS SLIDER
  17. COUNTER UP
  18. CHANGE FILE NAME OF FILE INPUT
  19. TOGGLING ADD PRODUCT FORM  BASED ON SELECTED PRODUCT TYPE
  20. APEXCHART
         
    */



  //****** 1. DATA BACKGROUND SET ******//
  $("[data-color-code]").each(function () {
    $(this).css("background-color", $(this).attr("data-color-code"));
  });
  $("[data-outline-color-code]").each(function () {
    $(this).css("outline-color", $(this).attr("data-outline-color-code"));
  });

  $("[data-background]").each(function () {
    $(this).css(
      "background-image",
      "url(" + $(this).attr("data-background") + ")"
    );
  });

  //****** 2. MOBILE MENU ******//
  const $overlay = $(".overlay");
  const $mobileMenu = $(".mobile-menu");

  $(".header-toggle, .mobile-menu-toggle").on("click", function () {
    $mobileMenu.toggleClass("active");
    $overlay.addClass("active");
    $("body").toggleClass("menu-open", $mobileMenu.hasClass("active"));
  });
  $(".mobile-menu .close").on("click", function () {
    $mobileMenu.removeClass("active");
    $overlay.removeClass("active");
    $("body").removeClass("menu-open");
  });

  //****** 3. STICKY HEADER ******//
  const $header = $(".header-top");
  $(window).on("scroll", function () {
    if ($(this).scrollTop() > 65) {
      $header.addClass("sticky");
    } else {
      $header.removeClass("sticky");
    }
  });

  //******  4. SEARCH BAR ******//
  const $searchIcon = $("#searchIcon");
  const $searchBar = $("#searchBar");

  $searchIcon.on("click", function () {
    $searchBar.addClass("show");
    $overlay.addClass("active");
  });


  //******  5. NICE SELECT ******//
  $(".nice-select").niceSelect();

  //****** 6. WOW JS ******//
  new WOW().init();

  //******  7. HIDE & SHOW PASSWORD ******//
  const $passwordInput = $("#create-password");
  const $eyeOffIcon = $(".eye-off");
  const $eyeOnIcon = $(".eye-on");

  $eyeOffIcon.on("click", function () {
    $passwordInput.attr("type", "text");
    $eyeOffIcon.hide();
    $eyeOnIcon.show();
  });

  $eyeOnIcon.on("click", function () {
    $passwordInput.attr("type", "password");
    $eyeOnIcon.hide();
    $eyeOffIcon.show();
  });


  // change pass input
  const $confirmPasswordInput = $("#confirm-password");
  const $confirmEyeOffIcon = $(".confirm-eye-off");
  const $confirmEyeOnIcon = $(".confirm-eye-on");

  $confirmEyeOffIcon.on("click", function () {
    $confirmPasswordInput.attr("type", "text");
    $confirmEyeOffIcon.hide();
    $confirmEyeOnIcon.show();
  });

  $confirmEyeOnIcon.on("click", function () {
    $confirmPasswordInput.attr("type", "password");
    $confirmEyeOnIcon.hide();
    $confirmEyeOffIcon.show();
  });

  //******  8. COUNTDOWN STARTS ******//
  const cd = document.getElementById("countdown-date");
  if (cd) {
    var countdownDate = new Date(cd.value).getTime();

    var countdownInterval = setInterval(function () {
      var now = new Date().getTime();
      var distance = countdownDate - now;
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
      );
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      document.getElementById("days").textContent = days;
      document.getElementById("hours").textContent = hours;
      document.getElementById("minutes").textContent = minutes;
      document.getElementById("seconds").textContent = seconds;

      if (distance < 0) {
        clearInterval(countdownInterval);
        document.getElementById("countdown").innerHTML = "<p>Deal Expired!</p>";
      }
    }, 1000);
  }

  $(".collapse-item").on("show.bs.collapse", function () {
    $(this).prev().find(".collapse-icon").removeClass("collapsed");
  });

  $(".collapse-item").on("hide.bs.collapse", function () {
    $(this).prev().find(".collapse-icon").addClass("collapsed");
  });

  // close overlay search bar and mobile menu
  $overlay.on("click", function () {
    $mobileMenu.removeClass("active");
    $overlay.removeClass("active");
    $searchBar.removeClass("show");
    $("body").removeClass("menu-open");
  });

  //******  9. HERO SECTION SLIDER ******//
  $(".hero-slider-wrapper").slick({
    dots: false,
    infinite: true,
    speed: 500,
    autoplay: true,
    fade: true,
    cssEase: "linear",
    arrows: false,
  });

  //******  10. HOME CATE SLIDER ******//
  $(".home-cate-slider").slick({
    dots: false,
    infinite: true,
    slidesToShow: 6,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    fade: false,
    cssEase: "linear",
    arrows: false,
    responsive: [
      {
        breakpoint: 1400,
        settings: {
          slidesToShow: 5,
        },
      },
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 4,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 425,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  });
  $(".home3-cate-slider").slick({
    dots: true,
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    speed: 500,
    autoplay: true,
    fade: false,
    cssEase: "linear",
    arrows: false,
    responsive: [
      {
        breakpoint: 1400,
        settings: {
          slidesToShow: 4,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 425,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  });

  //****** 11. PRODUCT DETAILS SLIDER ******//
  $(".product-main-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    // fade: true,
    asNavFor: ".product-nav-slider",
  });
  $(".product-nav-slider").slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: ".product-main-slider",
    dots: false,
    arrows: false,
    centerMode: true,
    focusOnSelect: true,
    centerPadding: "60px",
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 4,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 425,
        settings: {
          slidesToShow: 2,
        },
      },
 
    ],
  });

  //******  12. PRICE RANGE SLIDER ******//
  $(function () {
    const start_value = $("#start_value").val();
    const end_value = $("#end_value").val();
    const max_value = $("#max_value").val();

    $("#slider-range").slider({
      range: true,
      min: 0,
      max: max_value,
      values: [start_value, end_value],
      slide: function (event, ui) {
        $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
      },
    });
    $("#amount").val(
      "$" +
      $("#slider-range").slider("values", 0) +
      " - $" +
      $("#slider-range").slider("values", 1)
    );
  });

  //****** 13. ADD DYNAMIC CLASS FOR SVG ******//
  $(".gs-dashboard-user-sidebar-wrapper svg, .user-dropdown-wrapper svg").each(
    function () {
      var $svg = $(this);

      // Check each path element inside the SVG
      $svg.find("path").each(function () {
        var $path = $(this);

        // Check if the path has a fill attribute
        if ($path.attr("fill")) {
          $svg.addClass("has-fill");
        }

        // Check if the path has a stroke attribute
        if ($path.attr("stroke")) {
          $svg.addClass("has-stroke");
        }
      });
    }
  );

  //******  14. TOGGLE VENDOR DASHBOARD SIDEBAR ******//
  $(".gs-vendor-toggle-btn").on("click", function () {
    $(".gs-vendor-sidebar-wrapper").toggleClass("collapsed");
    $(".gs-vendor-header-outlet-wrapper").toggleClass("increased-width");
  });

  //******  15. DATA TABLE ******//
  // new DataTable("#example", {
  //   layout: {
  //     bottomEnd: {
  //       paging: {
  //         boundaryNumbers: false,
  //       },
  //     },
  //   },
  // });

  //******  16. PRODUCT CARDS SLIDER ******//
  $(".product-cards-slider").slick({
    dots: false,
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 4,
    speed: 500,
    autoplay: true,
    fade: false,
    cssEase: "linear",
    arrows: false,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
        },
      },
    ],
  });

  //******  16b. BRANDS CAROUSEL (mobile) ******//
  function initBrandsCarousel() {
    var $carousel = $(".ignavo-brands-carousel");
    if (!$carousel.length) {
      return;
    }

    if ($(window).width() < 768) {
      if (!$carousel.hasClass("slick-initialized")) {
        $carousel.slick({
          dots: false,
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          speed: 400,
          autoplay: true,
          autoplaySpeed: 2500,
          arrows: false,
          responsive: [
            {
              breakpoint: 480,
              settings: {
                slidesToShow: 2,
              },
            },
          ],
        });
      } else {
        $carousel.slick("setPosition");
      }
    } else if ($carousel.hasClass("slick-initialized")) {
      $carousel.slick("unslick");
    }
  }

  initBrandsCarousel();
  $(window).on("resize", function () {
    initBrandsCarousel();
  });

  //******  17. COUNTER UP ******//
  $(".counter").counterUp({
    delay: 10,
    time: 1000,
  });

  //******  18. CHANGE FILE NAME OF FILE INPUT  ******//
  $('.input-file').on("change", function () {
    var filename = $(this).val().split('\\').pop(); // Get the filename only
    $(this).siblings('.fileName').text(filename || "No file chosen"); // Update the corresponding label text
  });





  //****** 19. TOGGLING ADD PRODUCT FORM  BASED ON SELECTED PRODUCT TYPE ******//
  const $physicalProductInputesWrapper = $(".physical-product-inputes-wrapper");
  const $digitalProductInputesWrapper = $(".digital-product-inputes-wrapper");

  $(".physical-product-radio").on("click", function () {
    $physicalProductInputesWrapper.addClass("show");
    $digitalProductInputesWrapper.removeClass("show");
  });
  $(".digital-product-radio").on("click", function () {
    $digitalProductInputesWrapper.addClass("show");
    $physicalProductInputesWrapper.removeClass("show");
  });

  const $uploadByFile = $(".upload-by-file");
  const $uploadByUrl = $(".upload-by-url");

  $(".upload-by-file-radio").on("click", function () {
    $uploadByFile.addClass("show");
    $uploadByUrl.removeClass("show");
  });
  $(".upload-by-url-radio").on("click", function () {
    $uploadByUrl.addClass("show");
    $uploadByFile.removeClass("show");
  });

  //******  20. APEXCHART  ******//
  // var options = {
  //   colors: ['#27BE69'],
  //   series: [
  //     {
  //       name: 'Net Profit',
  //       data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 44, 55, 57, 56, 61, 58,]
  //     },
  //     // {
  //     //   name: 'Revenue',
  //     //   data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
  //     // },
  //     // {
  //     //   name: 'Free Cash Flow',
  //     //   data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
  //     // }
  //   ],
  //   chart: {
  //     type: 'bar',
  //     height: 450
  //   },
  //   plotOptions: {
  //     bar: {
  //       horizontal: false,
  //       columnWidth: '20%',
  //       endingShape: 'rounded',
  //       borderRadius: 8,
  //       borderRadiusApplication: 'end',
  //       borderRadiusWhenStacked: 'last'
  //     },
  //   },
  //   dataLabels: {
  //     enabled: false
  //   },
  //   stroke: {
  //     show: true,
  //     width: 2,
  //     colors: ['transparent']
  //   },
  //   xaxis: {
  //     categories: ['01 Jun', '03 Jun', '05 Jun', '07 Jun', '09 Jun', '11 Jun', '13 Jun', '15 Jun', '17 Jun', '19 Jun', '21 Jun', '23 Jun', '25 Jun', '27 Jun', '29 Jun',],
  //   },
  //   fill: {
  //     opacity: 1
  //   },
  //   tooltip: {
  //     y: {
  //       formatter: function (val) {
  //         return "$ " + val + " thousands"
  //       }
  //     }
  //   }
  // };
  // var chart = new ApexCharts($("#chart")[0], options);
  // chart.render();


  // Hide all other collapses
  $(".has-sub-menu a").on("click", function () {
    $(".collapse").not($(this).next(".collapse")).collapse("hide");
  });

  // vendor notification 
  $("#toggle-vendor-noti").on("click", function () {
    $(".gs-vendor-header-noti").toggleClass("active");
  
  });

  $(document).on("click", function (event) {
    if (!$(event.target).closest(".gs-vendor-header-noti, #toggle-vendor-noti").length) {
      $(".gs-vendor-header-noti").removeClass("active");
    }
  });



  $(window).on('resize', function() {
    $(".nicEdit-panelContain").parent().width("100%");
    $(".nicEdit-panelContain").parent().next().width("99.6%");
});

  //****** COOKIE CONSENT (GDPR) ******//
  (function () {
    var banner = document.getElementById("cookie-consent");
    if (!banner) return;

    var storageKey = "industrialmac_cookie_consent";
    var cookieName = "industrialmac_cookie_consent";
    var settingsPanel = document.getElementById("cookie-consent-settings");
    var analyticsInput = document.getElementById("cookie-pref-analytics");
    var marketingInput = document.getElementById("cookie-pref-marketing");

    function readConsent() {
      try {
        var stored = localStorage.getItem(storageKey);
        return stored ? JSON.parse(stored) : null;
      } catch (e) {
        return null;
      }
    }

    function writeConsent(consent) {
      var payload = {
        essential: true,
        analytics: !!consent.analytics,
        marketing: !!consent.marketing,
        timestamp: new Date().toISOString(),
        version: "1"
      };

      localStorage.setItem(storageKey, JSON.stringify(payload));

      var expires = new Date();
      expires.setFullYear(expires.getFullYear() + 1);
      document.cookie =
        cookieName +
        "=" +
        encodeURIComponent(JSON.stringify(payload)) +
        "; expires=" +
        expires.toUTCString() +
        "; path=/; SameSite=Lax";

      document.dispatchEvent(
        new CustomEvent("cookieConsentUpdated", { detail: payload })
      );
    }

    function hideBanner() {
      banner.classList.remove("is-visible");
      window.setTimeout(function () {
        banner.setAttribute("hidden", "hidden");
      }, 350);
    }

    function showBanner() {
      banner.removeAttribute("hidden");
      window.requestAnimationFrame(function () {
        banner.classList.add("is-visible");
      });
    }

    function applyPreferences(consent) {
      if (analyticsInput) analyticsInput.checked = !!consent.analytics;
      if (marketingInput) marketingInput.checked = !!consent.marketing;
    }

    function saveFromPreferences() {
      writeConsent({
        analytics: analyticsInput ? analyticsInput.checked : false,
        marketing: marketingInput ? marketingInput.checked : false
      });
      hideBanner();
    }

    if (readConsent()) {
      banner.setAttribute("hidden", "hidden");
      return;
    }

    showBanner();

    banner.addEventListener("click", function (event) {
      var action = event.target.closest("[data-cookie-action]");
      if (!action) return;

      var type = action.getAttribute("data-cookie-action");

      if (type === "accept-all") {
        writeConsent({ analytics: true, marketing: true });
        hideBanner();
        return;
      }

      if (type === "reject-all") {
        writeConsent({ analytics: false, marketing: false });
        hideBanner();
        return;
      }

      if (type === "toggle-settings" && settingsPanel) {
        var isOpen = !settingsPanel.hasAttribute("hidden");
        if (isOpen) {
          settingsPanel.setAttribute("hidden", "hidden");
          action.setAttribute("aria-expanded", "false");
        } else {
          settingsPanel.removeAttribute("hidden");
          action.setAttribute("aria-expanded", "true");
        }
        return;
      }

      if (type === "save-preferences") {
        saveFromPreferences();
      }
    });
  })();


});
