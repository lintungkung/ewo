//距離400px時出現To Top
$(window).scroll(function(){      
  var $window =  $(window).scrollTop();
  var $header = $('header');

  if($window > 100){
    $('a.totop').addClass('active');  
    $header.addClass('active');

  } else{
    $('a.totop').removeClass('active');  
    $header.removeClass('active');  
  }
}) 

$(function(){
  //To top effect   
  $('a.totop').on('click',function(){
    $('html, body').animate({scrollTop: 0}, 1500);
    return false;
  })   

  $('a.header-search').on('click',function(){

    $('.search-area').toggleClass('active');

    return false;

  })

  $('header .member-area ul li > a').on('click',function(){

    $(this).parent('li').toggleClass('active');

  })

})