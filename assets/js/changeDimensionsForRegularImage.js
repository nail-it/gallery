export default function() {
    $('.regular-image').each(function () {
        var s = $(this).attr('href').replace(/\?.*$/, '');
        $(this).attr('href', s + '?width=' + ($(window).width()) + '&height=' + ($(window).height()));
    });
}