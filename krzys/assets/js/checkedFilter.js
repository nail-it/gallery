export default function(value) {
    if (value == 1) {
        $('.selected1').show();
        $('.selected').show(); // valid till 6.01.2015
        $('.selected').each(function () {
            $(this).find('a.regular-image').attr('data-fancybox-group', 'button');
        });
        $('.selected1').each(function () {
            $(this).find('a.regular-image').attr('data-fancybox-group', 'button');
        });
        $('.selected2').hide();
        $('.selected2').each(function () {
            $(this).find('a.regular-image').attr('data-fancybox-group', '');
        });
        $('.son').show();
        $('.dau').hide();
    } else {
        if (value == 2) {
            $('.selected2').show();
            $('.selected2').each(function () {
                $(this).find('a.regular-image').attr('data-fancybox-group', 'button');
            });
            $('.selected1').hide();
            $('.selected1').each(function () {
                $(this).find('a.regular-image').attr('data-fancybox-group', '');
            });
            $('.selected').hide(); // valid till 6.01.2015
            $('.selected').each(function () {
                $(this).find('a.regular-image').attr('data-fancybox-group', '');
            });
            $('.son').hide();
            $('.dau').show();
        } else {
            $('.item-box').show();
            $('.admin-item').show();
            $('.symfony-content').find('a.regular-image').attr('data-fancybox-group', 'button');
            $('.son').show();
            $('.dau').show();

        }
    }
}