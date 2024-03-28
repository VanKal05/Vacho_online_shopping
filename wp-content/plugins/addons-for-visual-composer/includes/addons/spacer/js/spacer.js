jQuery(function ($) {


    var custom_css = '';
    $('.lvca-spacer').each(function () {

        spacer_elem = $(this);

        var id_selector = '#' + spacer_elem.attr('id');

        var desktop_spacing = (typeof spacer_elem.data('desktop_spacing') !== 'undefined') ? spacer_elem.data('desktop_spacing') : 50;

        var tablet_spacing = (typeof spacer_elem.data('tablet_spacing') !== 'undefined') ? spacer_elem.data('tablet_spacing') : 30;

        var tablet_width = spacer_elem.data('tablet_width') || 960;

        var mobile_spacing = (typeof spacer_elem.data('mobile_spacing') !== 'undefined') ? spacer_elem.data('mobile_spacing') : 10;

        var mobile_width = spacer_elem.data('mobile_width') || 480;

        custom_css += id_selector + ' { height:' + desktop_spacing + 'px; }';

        custom_css += ' @media only screen and (max-width: ' + tablet_width + 'px) { ' + id_selector + ' { height:' + tablet_spacing + 'px; } } ';

        custom_css += ' @media only screen and (max-width: ' + mobile_width + 'px) { ' + id_selector + ' { height:' + mobile_spacing + 'px; } } ';

    });
    if (custom_css !== '') {
        custom_css = '<style type="text/css">' + custom_css + '</style>';
        $('head').append(custom_css);
    }


});