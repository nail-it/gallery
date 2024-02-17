import changeDimensionsForRegularImage from './changeDimensionsForRegularImage';
import popupAlert from './popupAlert';

window.addEventListener("orientationchange", function () {
    changeDimensionsForRegularImage();
}, false);

// $(function () {
//     // there's the gallery and the best-add
//     var $gallery = $("#gallery");
//     var $bestadd = $("#best-add");
//
//     // image deletion function
//     var recycle_icon = "<a href='link/to/recycle/script/when/we/have/js/off' title='Recycle this image' class='ui-icon ui-icon-refresh'>Recycle image</a>";
//
//     function bestImage($item) {
//         $item.fadeOut(function () {
//             var $list = $("ul", $bestadd).length ?
//                 $("ul", $bestadd) :
//                 $("<ul class='gallery ui-helper-reset'/>").appendTo($bestadd);
//
//             $item.find("a.ui-icon-best-add").remove();
//             $item.append(recycle_icon).appendTo($list).fadeIn(function () {
//                 $item
//                     .animate({width: "38px"})
//                     .find("img")
//                     .animate({height: "26px"});
//             });
//         });
//     }
//
//     // image recycle function
//     var bestadd_icon = "<a href='link/to/best-add/script/when/we/have/js/off' title='Delete this image' class='ui-icon ui-icon-best-add'>Delete image</a>";
//
//     function recycleImage($item) {
//         $item.fadeOut(function () {
//             $item
//                 .find("a.ui-icon-refresh")
//                 .remove()
//                 .end()
//                 .css("width", "96px")
//                 .append(bestadd_icon)
//                 .find("img")
//                 .css("height", "72px")
//                 .end()
//                 .appendTo($gallery)
//                 .fadeIn();
//         });
//     }
//
//     // image preview function, demonstrating the ui.dialog used as a modal window
//     function viewLargerImage($link) {
//         var src = $link.attr("href"),
//             title = $link.siblings("img").attr("alt"),
//             $modal = $("img[src$='" + src + "']");
//
//         if ($modal.length) {
//             $modal.dialog("open");
//         } else {
//             var img = $("<img alt='" + title + "' width='384' height='288' style='display: none; padding: 8px;' />")
//                 .attr("src", src).appendTo("body");
//             setTimeout(function () {
//                 img.dialog({
//                     title: title,
//                     width: 400,
//                     modal: true
//                 });
//             }, 1);
//         }
//     }
//
//     // resolve the icons behavior with event delegation
//     $("ul.gallery > li").click(function (event) {
//         var $item = $(this),
//             $target = $(event.target);
//
//         if ($target.is("a.ui-icon-best-add")) {
//             deleteImage($item);
//         } else if ($target.is("a.ui-icon-zoomin")) {
//             viewLargerImage($target);
//         } else if ($target.is("a.ui-icon-refresh")) {
//             recycleImage($item);
//         }
//
//         return false;
//     });
// });

$(document).ready(function () {

    $('.rotate-left').on('click', function () {
        //$(this).children('img').hide();
        //$(this).append('<div class="ajax-loader"></div>');

        var url = $(this).attr('id');
        var me = $(this);
        var rr = $.post(url, {},
            function (data) {
                me.parent().prev().children('img').fadeOut('500', function () {
                    $(this).remove();
                    me.parent().prev().append(data.image).hide().fadeIn('2000');
                });
            });
        return false;
    });

    $('.rotate-right').on('click', function () {
        var url = $(this).attr('id');
        var me = $(this);
        var rr = $.post(url, {},
            function (data) {
                me.parent().prev().children('img').fadeOut('500', function () {
                    $(this).remove();
                    me.parent().prev().append(data.image).hide().fadeIn('2000');
                });
            });
        return false;
    });

    $('.hide-pic').on('click', function () {
        var url = $(this).attr('id');
        var me = $(this);
        var rr = $.post(url, {},
            function (data) {
                me.parent().parent().fadeOut('500');
            });
        return false;
    });

    var dataAmount = 0;
    var incr = 0;
    var url = '';

    function doAjax() {
        if (incr < dataAmount) {
            var rr2 = $.post(url).done(function (data) {
                incr++;
                $('#amount-current').html(parseInt($('#amount-current').html()) + 1);
                doAjax();
            });
        }
    }

    $('.incoming-process').on('click', function () {
        $('#incoming-block').show();
        var url2 = $(this).attr('id');
        url = $(this).attr('title');

        var rr = $.post(url2).done(function (data) {
            dataAmount = data.amount;
            $('#amount-all').html(dataAmount);
            doAjax();
        });
        return false;
    });

    $(".admin-picture").on('mousedown', function (e) {
        var a = null;
        var s = '';
        if ((e.which == 1)) {
            if ($(this).parent().hasClass('selected1')) {
                $(this).parent().removeClass('selected1');
                a = 'r';
                s = 1;
            } else {
                $(this).parent().addClass('selected1');
                a = 'a';
                s = 1;
            }
            $(this).parent().removeClass('selected2');
        }
        if ((e.which == 3)) {
            if ($(this).parent().hasClass('selected2')) {
                $(this).parent().removeClass('selected2');
                a = 'r';
                s = 2;
            } else {
                $(this).parent().addClass('selected2');
                a = 'a';
                s = 2;
            }
            $(this).parent().removeClass('selected1');
        } else if ((e.which == 2)) {
            $(this).parent().removeClass('selected1');
            $(this).parent().removeClass('selected2');
            a = 'r';
        }
        var url = $(this).attr('id') + '?action=' + a + '&select=' + s;
        var rr = $.post(url, {},
            function (data) {

            });
        e.preventDefault();
    }).on('contextmenu', function (e) {
        e.preventDefault();
    });

    $('div.item').mouseenter(function () {
        $(this).find('span').stop(true, true).slideUp();
    }).mouseleave(function () {
        $(this).find('span').stop(true, true).slideDown();
    });

    $('.open-months').click(function () {
        $('.open-months').removeClass('active');
        $(this).addClass('active');

        $('.calendar-months').hide();
        $('.calendar-months-' + $(this).attr('title')).show();
    });

    $('input.description').keyup(function (event) {
        var url = $(this).attr('id');
        $.post(url, {description: $(this).val()},
            function (data) {
            });
    });

    $('input.description-day').keyup(function (event) {
        var url = $(this).attr('id');
        $.post(url, {description: $(this).val()},
            function (data) {
            });
    });

    $('.evolution-add').click(function () {
        var url = $(this).attr('id');
        var rr = $.post(url, {},
            function (data) {
                popupAlert('Added to evolution successfully!', 'success');
            });
        return false;

    });

    $('.actions span').hover(function () {
        $(this).addClass('hover');
    }, function () {
        $(this).removeClass('hover');
    })

    $('.best-add').click(function () {
        var url = $(this).attr('id');
        var rr = $.post(url, {},
            function (data) {
                popupAlert('Added to best successfully!', 'success');
            });
        return false;
    });

    changeDimensionsForRegularImage();

});