// Lighbox gallery
jQuery('#popup-gallery').each(function () {
    jQuery(this).magnificPopup({
        delegate: 'a.popup-gallery-image',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});
