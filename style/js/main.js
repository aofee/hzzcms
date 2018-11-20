/**
 * Created by cdc on 2018/10/30.
 */
$('.nav_left_768').click(function () {

    if ($('.nav_right').hasClass('nav_right_show')) {

        $('.nav_right').removeClass('nav_right_show');
        $('.nav_left_768_i ').removeClass('nav_left_768_i_tr');
        $('.nav_left_768_i').attr('src', '/images/list.png');

    } else {
        $('.nav_right').addClass('nav_right_show');
        $('.nav_left_768_i ').addClass('nav_left_768_i_tr');
        $('.nav_left_768_i').attr('src', '/images/chahao.png');
    }
// 折叠按钮的样式


    for (var i = 0; i <= 10; i++) {
        (function (i) {
            $(".nav_tr").eq(i).on("click", function () {
                if ($('.nav_child').eq(i).hasClass('nav_child_show')) {
                    $('.nav_child').removeClass('nav_child_show');
                    $('.nav_tr').removeClass('nav_tr_show');
                } else {
                    $('.nav_child').removeClass('nav_child_show');
                    $('.nav_tr').removeClass('nav_tr_show');

                    $('.nav_child').eq(i).addClass('nav_child_show');
                    $('.nav_tr').eq(i).addClass('nav_tr_show');
                }

            });
        })(i);
    }
    ;
    // $('.nav_right').toggleClass('nav_right_show');
    // $('.nav_left_768_i').toggleClass('glyphicon-remove');
});
// 导航手机端兼容样式结束

for (var j = 0; j <= 10; j++) {
    (function (j) {


        $(".change-xinwen").eq(j).on("click", function () {

            $('.change-xinwen').removeClass('xinwen-active');
            $('.change-xinwen').eq(j).addClass('xinwen-active');
            $('.index-xinwen-title').text($('.change-xinwen').eq(j).data('title'));
            $('.index-xinwen-con').text($('.change-xinwen').eq(j).data('con'));
            $('.index-xinwen-img').attr('src', $('.change-xinwen').eq(j).data('img'));
        });
    })(j);
}
;


for (var j = 0; j <= 10; j++) {
    (function (j) {


        $(".join-slide3-list").eq(j).on("click", function () {
            console.log(j);
            $('.join-slide3-list-main').eq(j).toggleClass('join-slide3-list-main-show');
            $('.join-slide3-list').eq(j).toggleClass('join-slide3-tr');

        });
    })(j);
}
;