jQuery(function($){
    $('body').on('click', '#_cutom_meta_key_check', function(e){
        
       if($(this).prop("checked") == true){
               $('#customer_price').prop('required',true);
              $('#distributor_price').prop('required',true);
        }
        else if($(this).prop("checked") == false){

            $('#customer_price').prop('required',false);
            $('#distributor_price').prop('required',false);

             
        }
  
        
    });
});