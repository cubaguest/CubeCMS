CubeCMS.Shop = {
   combinations : new Array(),
   currency : '',
   roundDecimals : 0,
   decimals : 0,
   product : {
      code: null,
      price: 0,
      tax: 0,
      weight: 0,
      qty: 0,
      unit: 'ks'
   },
   setProduct : function (product){
      this.product = $.extend(this.product, product);
   },

   addCombination : function(id, varArray, priceAdd, weightAdd, codesArr, qty){
      var comb = { id : id, variants : varArray, price : priceAdd, weight : weightAdd, codes : codesArr, qty : qty };
      this.combinations.push(comb);
   },

   getProductPrice: function(price, tax){
      return this.roundPrice(price*(1+tax), 10);
   },

   roundPrice : function(price, decimals){
      if(typeof (decimals) == "undefined"){
         decimals = 1;
      }
      decimals = decimals*10;
      return decimals == 0 ? Math.round(price) : Math.round(price*10*decimals)/(10*decimals);
   },
   getProductCode : function(variantCodes){
      var code = this.product.code;
      $.each(variantCodes, function(id, c){
         var replacement = "{"+id+"}";
//         console.log(replacement, c, code.replace(replacement, c));
         code = code.replace(replacement, c);
      });
      return code;
   },
   getFormattedPrice : function(price, currency){
      if(typeof (currency) == "undefined"){
         currency = true;
      }
      price = parseFloat(price);
      // zaokrouhlení
      if(this.roundDecimals > 0){
         price = Math.round(price * 10 * this.roundDecimals) / (10 * this.roundDecimals);
      } else {
         price = Math.round(price);
      }
      // úprava desetiných míst
      return price.toFixed(this.decimals).toString().replace('.', ',') + (currency ? " " + CubeCMS.Shop.currency : '' );
   }
}

// extend string for replace
String.prototype.format = function() {
   var args = arguments;
   return this.replace(/{(\d+)}/g, function(match, number) {
      return typeof args[number] != 'undefined' ? args[number] : match;
   });
};