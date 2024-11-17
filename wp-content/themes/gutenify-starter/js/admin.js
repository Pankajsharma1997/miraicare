"use strict";

var _wp = wp,
    apiFetch = _wp.apiFetch;
jQuery(function ($) {
  var gutenify_starterRedirectToKitPage = function gutenify_starterRedirectToKitPage(res) {
    // if( res?.status && 'active' === res.status ) {
    window.location = "".concat(window.gutenify_starter.gutenify_kit_gallery); // }
  }; // Activate Gutenify.


  $(document).on('click', '.gutenify-starter-activate-gutenify', function () {
    $(this).html('<span class="dashicons dashicons-update"></span> Loading...').addClass('gutenify-starter-importing-gutenify');
    apiFetch({
      path: '/wp/v2/plugins/gutenify/gutenify',
      method: 'POST',
      data: {
        "status": "active"
      }
    }).then(function (res) {
      gutenify_starterRedirectToKitPage(res);
    })["catch"](function () {
      gutenify_starterRedirectToKitPage({});
    });
  });
  $(document).on('click', '.gutenify-starter-install-gutenify', function () {
    $(this).html('<span class="dashicons dashicons-update"></span> Loading...').addClass('gutenify-starter-importing-gutenify');
    apiFetch({
      path: '/wp/v2/plugins',
      method: 'POST',
      data: {
        "slug": "gutenify",
        "status": "active"
      }
    }).then(function (res) {
      gutenify_starterRedirectToKitPage(res);
    })["catch"](function () {
      gutenify_starterRedirectToKitPage({});
    });
  });
  $(document).on('click', '.gutenify-starter-admin-notice .notice-dismiss', function () {
    console.log(ajaxurl + "?action=gutenify_starter_hide_theme_info_noticebar");
    apiFetch({
      url: ajaxurl + "?action=gutenify_starter_hide_theme_info_noticebar&gutenify_starter-nonce-name=".concat(window.gutenify_starter.gutenify_starter_nonce),
      method: 'POST'
    }).then(function (res) {
      console.log(res);
    })["catch"](function (res) {
      console.log(res);
    });
  });
});