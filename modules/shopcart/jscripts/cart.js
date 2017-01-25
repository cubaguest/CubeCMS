Cart = {
   cart : {
      shippings : [],
      freeShipAndPayFrom : -1,
      payments : [],
      disallowPayments : [],
      priceCart : 0.0,
      priceShipping : 0,
      pricePayment : 0,
      updateUrl : '',
      currency : 'Kč',
      datepicker : false,
      roundDecimals : 0,
      decimals : 0,
      msg : {
         free : 'Free!',
         deleteItem : 'Delete item?'
      }
   },
   // setup cart params
   init : function(params) {
      this.cart = $.extend(this.cart, params);

      $('.cart-item').each(function(){
         var uSize = $(this).find('.unit-size').val();
//            var max = null;
//            if($(this).find('.max-qty').length > 0){
//               max = parseInt($(this).find('.max-qty').val());
//            }

         $('.shop-cart-qty', this ).spinner({
            min: uSize,
            step : uSize,
            //               max : max,
            stop : function(e, ui){
               var $row = $(this).closest('tr.cart-item');
               var id = $row.attr('id').replace('cart-item-', '');
               // ajax update qty
               $.ajax({ type: "POST", cache : false,
                  url: Cart.cart.updateUrl,
                  data: {
                     action: 'updateQty',
                     id : id,
                     qty : $(this).val()
                  },
                  success: function(data) {
                     // update product price
                     $('.product-price-input', $row).val(data.price);
                     $('.product-price', $row).text(Cart.getFormattedPrice(data.price));
                     // update cart
                     Cart.update();
                  }
               });
            }
         });
      });

      // remove item
      $('.button-delete-item').click(function(){
         if(!confirm(Cart.cart.msg.deleteItem)){
            return false;
         }
         var $row = $(this).closest('tr.cart-item');
         var id = $row.attr('id').replace('cart-item-', '');
         $.ajax({
            type: "POST",
            url: Cart.cart.updateUrl,
            data : {action : 'delete', id: id},
            cache : false,
            success: function(data) {
               // remove row
               if(data.errmsg.length == 0 && data.deleted){
                  // if num rows == 0 reload page
                  $row.remove();
                  if($('#table-cart-items tbody tr').length == 0){
                     window.location.reload();
                  }
                  // update cart
                  Cart.update();
               } else {
                  alert('Chyba při mazání položky');
               }
            }
         });
         return false
      });
      // change shipping
      $('select[name="goto_order_shipping"]').change(function(){
         var info = Cart.cart.shippings[$(this).val()];
         $('.cart-shipping-note').text(info.note);
         info.note === "" ? $('.cart-shipping-note').hide() : $('.cart-shipping-note').show();
         Cart.updateAllowedPayments();
         Cart.updateShippingPrice($(this).val());
      });
      // change payment
      $('select[name="goto_order_payment"]').change(function(){
         var info = Cart.cart.payments[$(this).val()];
         $('.cart-payment-note').text(info.note);
         info.note === "" ? $('.cart-payment-note').hide() : $('.cart-payment-note').show();
         Cart.updatePaymentPrice($(this).val());
      });

      // date
      if(Cart.cart.datepicker){
         $('input[name="goto_order_pickupDate"]').datepicker({
            minDate : new Date()
         });
      }

      this.updateShipAndPayItems();
      this.updateAllowedPayments();
   },

   getFormattedPrice : function(price){
      price = parseFloat(price);
      // zaokrouhlení
      if(this.cart.roundDecimals > 0){
         price = Math.round(price * 10 * this.cart.roundDecimals) / (10 * this.cart.roundDecimals);
      } else {
         price = Math.round(price);
      }
      // úprava desetiných míst
      return price.toFixed(this.cart.decimals).toString().replace('.', ',') + " " + this.cart.currency;
   },

   update : function(){
      // aktualizace ceny produktů
      this.updateProductsPrice();

      // aktualizace položek dopravy a aplateb
      this.updateShipAndPayItems();
   },

   updateShipAndPayItems : function(){
      // shippings
      var $shipSelect = $('select[name="goto_order_shipping"]');
      var sel = $shipSelect.val();
      $shipSelect.html(null);
      $.each(this.cart.shippings, function(index, val){
         var name = val.name;
         if (val.price != "0"){
            if(Cart.isFreeShipAndPay()){
               name += " ("+Cart.cart.msg.free+")";
            } else {
               name += " ("+Cart.getShipOrPayPrice( val.price )+")";
            }
         }
         $shipSelect.append( $('<option></option>').attr({ value : index }).text(name) );
      });
      $shipSelect.val( sel );
      this.updateShippingPrice(sel)

      // payments
      var $paymentSelect = $('select[name="goto_order_payment"]');
      var sel = $paymentSelect.val();
      $paymentSelect.html(null);
      $.each(this.cart.payments, function(index, val){
         var name = val.name;
         if (val.price != "0"){
            if (Cart.isFreeShipAndPay()){
               name +=  " ("+Cart.cart.msg.free+")";
            } else {
               name +=  " ("+Cart.getShipOrPayPrice( val.price )+")";
            }
         }
         $paymentSelect.append( $('<option></option>').attr({ value : index }).text(name) );
      });
      $paymentSelect.val(sel);
      this.updatePaymentPrice(sel);
   },

   isFreeShipAndPay : function(){
      return (this.cart.freeShipAndPayFrom > 0 && this.cart.priceCart >= this.cart.freeShipAndPayFrom);
   },
   // vrací cenu, pokud obsahuje procento, vypočítá cenu
   getShipOrPayPrice : function(price) {
      if(price.toString().indexOf("%") != -1){ // price je procento z ceny
         var priceProd = Math.floor( this.cart.priceCart*( parseInt( price ) )/100 )
         return price + " / " + this.getFormattedPrice(priceProd);
      } else {
         return this.getFormattedPrice(price);
      }
   },

   updateProductsPrice : function() {
      var price = 0;
      $('.cart-item').each(function(){
         price += parseFloat( $(this).find('.product-price-input').val() );
      });
      $('.products-price').text(this.getFormattedPrice(price));
      this.cart.priceCart = price;
   },
   updateFullPrice : function() {
      $('#full-price').text( this.getFormattedPrice( this.cart.priceCart + this.cart.priceShipping + this.cart.pricePayment) );
   },

   updateAllowedPayments : function() {
      var $paySelect = $('select[name="goto_order_payment"]');
      $('option', $paySelect).removeAttr('disabled');
      var idShip = $('select[name="goto_order_shipping"]').val();
      $.each(Cart.cart.disallowPayments[idShip], function(){
         $('option[value="'+this+'"]', $paySelect).attr('disabled', 'disabled');
      });
      $('option:not(:disabled):first', $paySelect).prop('selected', true);
      $paySelect.change(); // kvuli přepočtu ceny
   },

   updateShippingPrice :function(idshipping) {
      var priceText;
      if(this.isFreeShipAndPay()){
         priceText = this.cart.msg.free;
         this.cart.priceShipping = 0;
      } else {
         if(this.cart.shippings[idshipping].price.indexOf("%") != -1){
            this.cart.priceShipping = Math.floor( this.cart.priceCart*( parseInt( this.cart.shippings[idshipping].price ))/100 );
            priceText = this.cart.priceShipping != 0 ?
               this.cart.shippings[idshipping].price + " / " + this.getShipOrPayPrice( this.cart.priceShipping ) : this.cart.msg.free;
         } else {
            this.cart.priceShipping = parseInt( this.cart.shippings[idshipping].price );
            priceText = this.cart.priceShipping != 0 ?
               this.getShipOrPayPrice( this.cart.priceShipping ) : this.cart.msg.free;
         }
      }
      $('#shipping-price').text(priceText);
      this.updateFullPrice();
   },

   updatePaymentPrice : function(idpayment) {
      var priceText;
      if(this.isFreeShipAndPay()){
         priceText = this.cart.msg.free;
         this.cart.pricePayment = 0;
      } else {
         if(this.cart.payments[idpayment].price.indexOf("%") != -1){
            this.cart.pricePayment = Math.floor( this.cart.priceCart*( parseInt( this.cart.payments[idpayment].price ) )/100 );
            priceText = this.cart.pricePayment != 0 ?
               this.cart.payments[idpayment].price + " / " +
               this.getShipOrPayPrice( this.cart.pricePayment ) : this.cart.msg.free;
         } else {
            this.cart.pricePayment = parseInt( this.cart.payments[idpayment].price );
            priceText = this.cart.pricePayment != 0 ?
               this.getShipOrPayPrice( this.cart.pricePayment ) : this.cart.msg.free;
         }
      }
      $('#payment-price').text(priceText);
      this.updateFullPrice();
   }
}