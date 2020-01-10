<template>
  <div class="container">
      <div class="element">
        <div class="loading2">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>    
  </div>
</template>

<script>

export default {

    data () {
    return {
      selectedProduct: null,
      info:null,
      products: [
       
        ],
        defaultProduct: [{
        name: "",
        cost: "",
        weight: "",
        width: "",
        height: "",
        length: ""
         }]
        }
    },
     methods :
   {
     addForm () {
       if (this.selectedProduct==null) {
          this.$store.state.calculator.push(this.defaultProduct);
       }
       else {
          var product = this.products.filter((item) =>{
            return item.id==this.selectedProduct;
          })
          this.$store.state.calculator.push(product[0]);
       }
     },
     test() {
       var products=null;
     this.$http.get("/products.txt").then(response => {
        this.info = response.data;
        this.buildList(this.info);
        }, response => {
     });
     }, 

    buildList(str) {
        var lines = str.split(';');
        if (lines.length>0) {
            for (var i=0; i<lines.length-1; i++) {
                var line=lines[i].split(':');
                var p = line[3].split('х');
                var params = {
                    id:i,
                    name:line[0]+" за "+line[1],
                    cost:line[1],
                    weight:line[2],
                    width:p[0],
                    height:p[1],
                    length:p[2]
                } 
                this.products.push(params);
            }
        }
    }

   },
   mounted () {
     this.test();
   }
}
</script>

<style>

.loading2 {
    height:140px;
    position:relative;
    width:140px;
    margin: 0 auto;

    /* css3 transform - scale */
    -webkit-transform:scale(0.6);
    -moz-transform:scale(0.6);
    -o-transform:scale(0.6);
}
.loading2 > div {
    background-color:#FFFFFF;
    height:25px;
    position:absolute;
    width:25px;

    /* css3 radius */
    -moz-border-radius:15px;
    -webkit-border-radius:15px;
    border-radius:15px;

    /* css3 animation */
    -webkit-animation-name:loading2;
    -webkit-animation-duration:1.04s;
    -webkit-animation-iteration-count:infinite;
    -webkit-animation-direction:linear;
    -moz-animation-name:loading2;
    -moz-animation-duration:1.04s;
    -moz-animation-iteration-count:infinite;
    -moz-animation-direction:linear;
    -o-animation-name:loading2;
    -o-animation-duration:1.04s;
    -o-animation-iteration-count:infinite;
    -o-animation-direction:linear;
}
.loading2 > div:nth-child(1) {
    left:0;
    top:57px;

    /* css3 animation */
    -webkit-animation-delay:0.39s;
    -moz-animation-delay:0.39s;
    -o-animation-delay:0.39s;
}
.loading2 > div:nth-child(2) {
    left:17px;
    top:17px;

    /* css3 animation */
    -webkit-animation-delay:0.52s;
    -moz-animation-delay:0.52s;
    -o-animation-delay:0.52s;
}
.loading2 > div:nth-child(3) {
    left:57px;
    top:0;

    /* css3 animation */
    -webkit-animation-delay:0.65s;
    -moz-animation-delay:0.65s;
    -o-animation-delay:0.65s;
}
.loading2 > div:nth-child(4) {
    right:17px;
    top:17px;

    /* css3 animation */
    -webkit-animation-delay:0.78s;
    -moz-animation-delay:0.78s;
    -o-animation-delay:0.78s;
}
.loading2 > div:nth-child(5) {
    right:0;
    top:57px;

    /* css3 animation */
    -webkit-animation-delay:0.91s;
    -moz-animation-delay:0.91s;
    -o-animation-delay:0.91s;
}
.loading2 > div:nth-child(6) {
    right:17px;
    bottom:17px;

    /* css3 animation */
    -webkit-animation-delay:1.04s;
    -moz-animation-delay:1.04s;
    -o-animation-delay:1.04s;
}
.loading2 > div:nth-child(7) {
    left:57px;
    bottom:0;

    /* css3 animation */
    -webkit-animation-delay:1.17s;
    -moz-animation-delay:1.17s;
    -o-animation-delay:1.17s;
}
.loading2 > div:nth-child(8) {
    left:17px;
    bottom:17px;

    /* css3 animation */
    -webkit-animation-delay:1.3s;
    -moz-animation-delay:1.3s;
    -o-animation-delay:1.3s;
}

/* Кадры анимации - Индикатор 2 */
@-webkit-keyframes loading2 {
    0%{ background-color:#80D519 }
    100%{ background-color:#FFFFFF }
}
@-moz-keyframes loading2 {
    0%{ background-color:#80D519 }
    100%{ background-color:#FFFFFF }
}
@-o-keyframes loading2 {
    0%{ background-color:#80D519 }
    100%{ background-color:#FFFFFF }
}

</style>