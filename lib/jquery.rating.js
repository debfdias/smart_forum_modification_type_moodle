(function($) {
  var buildRating = function(obj) {
    var rating=  averageRating(obj),
        obj    = buildInterface(obj),
        stars  = $("div.star", obj),
        cancel = $("div.cancel", obj)


    
      
        var fill = function() {
          drain();
          $("a", stars).css("width", "100%");
		  el = stars.index(this) + 1;
          for(x = 0; x < el; x++)
				stars.eq(x).addClass("hover");
		  //stars.lt(stars.index(this) + 1).addClass("hover");
        },
        drain = function() {
          stars.removeClass("on").removeClass("hover");
        },
        reset = function() {
          drain();
          el = rating[0];
		  for(x = 0; x < el; x++)
			stars.eq(x).addClass("on");
                         
        },
        cancelOn = function() {
          drain();
          $(this).addClass("on");
        },
        cancelOff = function() {
          reset();
          $(this).removeClass("on")
        }

   if(rating ==0){
       stars
      .hover(fill, reset).focus(fill).blur(reset)
       .click(function() {
        


    if (confirm('Deseja avaliar essa recomendaçao?')) {
       
       rating = [stars.index(this) + 1, 0];
       
       $.post(obj.url, { rating: $("a:first", this)[0].href.slice(1), id: $("a:first", this)[0].id.slice(0)});
       drain();
       el = rating[0];
		  for(x = 0; x < el; x++)
			stars.eq(x).addClass("on");
       var link = obj.url;
       var nlink = link.split('=');
       $('.l'+nlink[1]).html('Avaliado:');

       return false;
   }
   else {
       return false;
          }


       
          
  
         });
        
      
      
      }

    reset();
    return obj;
 
  }

  var buildInterface = function(form) {
      var idw=$(".rating select").attr("id");
     //var idw=$(".rating select").attr(id).val();
      
      var container = $("<div></div>").attr({"title": form.title, "class": form.className});
    $.extend(container, {url: form.action})
    var optGroup  = $("option", $(form));
	var input  = $("input", $(form));
    var size      = optGroup.length;
    optGroup.each(function() {
  
     container.append($('<div class="star"><a id="' + input.val() + '" href="#' + this.value + '" title="avaliar ' + this.value + '/'+ size +'">' + this.value + '</a></div>'));
 
      //container.append($('<div id ="'+idw+'" class="star" ><a id="' + input.val() + '" href="#' + this.value + '" title="Estrela ' + this.value + '/'+ size +'">' + this.value + '</a></div>'));
    });
    $(form).after(container).remove();
    return container;
  }

  var averageRating = function(el) { return el.title.split(":")[1].split(".") }

  $.fn.rating = function() { return $($.map(this, function(i) { return buildRating(i)[0] })); }
        
	if ($.browser.msie) try { document.execCommand("BackgroundImageCache", false, true)} catch(e) { }        

})(jQuery)