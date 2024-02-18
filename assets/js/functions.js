import changeDimensionsForRegularImage from './changeDimensionsForRegularImage';
import checkedFilter from './checkedFilter';

$(document).ready(function () {

    changeDimensionsForRegularImage();

    $('.regular-image').fancybox({
        padding: 0,
        margin: 0,
        type: 'ajax',
        prevEffect: 'fade',
        nextEffect: 'fade',
        openOpacity: 0,
        closeOpacity: 0,
        scrolling: 'no',
        autoSize: true,
        closeBtn: false,
        playSpeed: 5000,
        toolbar: true,
        // afterShow : function() {
        // 	var src = this.element.find('img').parent().attr('href');
        // 	var fancyImage = this;
        // 	setTimeout(function(){
        //         fancyImage.parent.find('#fancybox-buttons').find('.btnToggle').attr('href', src);
        //if(fancyImage.parent.find('.fancybox-wrap').height() + 30 > $( window ).height()) {
        //	fancyImage.parent.find('.fancybox-wrap').css('top', '0');
        //}
        // }, 1000);
        // },
        // onUpdate 		: function(opts, obj) {
        // 	$.fancybox.reposition();
        // },
        helpers: {
            title: {type: 'inside'},
            buttons: {}
        },
    });

    $('.open-months').click(function () {
        $('.open-months').removeClass('active');
        $(this).addClass('active');

        $('.calendar-months').hide();
        $('.calendar-months-' + $(this).attr('title')).show();
    });

    $('#filter > div').click(function () {
        $(this).children('input').attr('checked', true);
        var url = $(this).parent().attr('title') + '?tag=' + $(this).children('input').attr('value');
        var rr = $.post(url, {},
            function (data) {

            });
        checkedFilter($(this).children('input').attr('value'));
    });


    $('#filter > div > input').each(function () {
        if ($(this).attr('checked') == 'checked') {
            checkedFilter($(this).attr('value'));
        }
    });

});