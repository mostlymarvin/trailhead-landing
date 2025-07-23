(function($){
  document.getElementById('blog-categories').onchange = function(){
      // if value is category id
      if( this.value !== '-1' ){
          window.location='/category/'+this.value
          //window.location='/category/'+this.value
      }
  }

  document.getElementById('form-clear').onclick = function(e){
      e.preventDefault();
      document.getElementById('blog-categories').value = 0;
      document.getElementById('blog-search').reset();
  }

  
})(jQuery);