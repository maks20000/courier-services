<template>
  <div class="container">
    <div class="form">
        <h1>Расчет стоимости доставки игр Денежный Поток / Cashflow / Крысиные бега</h1>
       <div class="selectList">
        <select name="products" id="products" v-model="selectedProduct">
            <option v-for="product in products" :value="product.id">{{product.nameCost}}</option>
        </select>
        <button @click="AddProduct">+</button>
        </div>
        <div>
            <div class="product_line" v-for="item in addedProducts"><div><b>{{products[item].name}}</b> за {{products[item].cost}}</div>
                <div class="input-cost">
                  <input class="cost" type="number" @input="chengeCost(item, $event)" :placeholder="products[item].cost">
                  <span @click="DeleteProduct(item)" class="close-btn">-</span>
                </div>
            </div>
        </div>
        <div class="input_line">
            <div>
                <label for="cash">Оценочная стоимость</label>
                <div class="cash"><input step="any" type="number" name="ordersum" v-model.number="param.orderSum"></div>    
           </div>
           <div>
                <label for="cash">Наложенный платеж</label>
                <div class="cash"><input step="any" type="number" name="cash" v-model.number="param.cost"></div>    
           </div>
           <div>
                <label for="endCity">Регион получателя</label>
                <div class="endCity">
                <input name="region" list="regions" @change="setCity" v-model.trim="selectedRegion"/>
                      <datalist id="regions">
                        <option v-for="region in regionList" :value="region.name" />
                      </datalist>
                </div>  
           </div>
            <div>
                <label for="endCity">Город получения</label>
                <div class="endCity"><input type="text" name="endCity" list="city" v-model.trim="param.endCity"></div> 
                    <datalist id="city">
                      <option v-for="city in cityList" :value="city.name" />
                      </datalist> 
           </div>
        </div>
        <div class="input_line">
          <div class="weight">
            <label for="weight">Вес (кг)</label>
            <input step="any" type="number" name ="weight" v-model.number="param.weight" >
          </div>
          <div class="length">
            <label for="length">Длина (см)</label>
            <input step="any" type="number" name ="length" v-model.number="param.length">
          </div>
          <div class="width">
            <label for="width">Ширина (см)</label>
            <input step="any" type="number" name ="width" v-model.number="param.width">
          </div>
          <div class="height">
            <label for="height">Высота (см)</label>
            <input step="any" type="number" name ="height" v-model.number="param.height">
          </div>
        </div>
        <div class="button">
          <button class="result-btn" :disabled="buttonDisble" @click="GetPrices">Расчитать</button>
        </div>
        <div class="result"> 
            <indicator v-if="loading"></indicator>
            <div v-for="(items, key) in info" class="result-row">
                  <div class="result-type">{{GetType(key)}}</div>
                  <div class="result-data">
                    <div v-for="item in items" class="result-items">
                      <div class="item name"><div>{{item['name']}}</div></div>
                      <div class="item delivery"><div :class="{red: CheckDelivery(item.delivery)}" >{{item.delivery}}</div></div>
                      <div class="item price"><div :class="{red: CheckSum(SetPrice(item))}">{{SetPrice(item)}}</div></div> 
                      <div class="item tariff"><div>{{TariffText(item)}}</div></div>
                      <div class="item sum"><div>{{SetSum(item)}}</div></div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>

import indicator from './indicator'
export default {
  components: {indicator,},
  props : [
      'index',
      'weight',
      'length',
      'width',
      'height',
      'endCity',
      'cost',
      'product',
      'defaultProps'],
  data () {
    return {
      loading: false,
      selectedProduct: null,
      products: [],
      addedProducts:[],
      id: this.index,
      info : null,
      test: null,
      regionList:[],
      cityList:[],
      selectedRegion: "",
      arrayRegion:null,
      param: {
        weight: 0,
        length: 0,
        width: 0,
        height: 0,
        endCity: "",
        cost: 0,
        orderSum: 0,
        product: "",
        region: {
          name: "",
          regionCode: "",
          region : ""
        }
      }
    }
  },
   methods: 
    {

      chengeCost(item, event) {
          this.products[item].cost = Number.parseFloat(event.target.value)
          this.updateForm ();
      },

      setCity() {
          var region = this.selectedRegion;
          var newCity=[];
          this.cityList=this.arrayRegion.city;
          this.regionList.forEach((item)=>{
            if (item.name.toLowerCase().indexOf(region.toLowerCase())>=0) {

              this.arrayRegion.city.forEach((city) => {
                if (city.id_region==item.id) {
                  newCity.push(city)
                }
              })
            }
          })
          this.cityList=newCity;
      },

      DeleteProduct (index) {
          
          for (var i=0; i<this.addedProducts.length; i++) {
            if (this.addedProducts[i]==index) 
             {
                this.addedProducts.splice(i,1)
                break;
             }
          }
          if (this.addedProducts.length==0) {
            this.param.width=0
            this.param.length=0
          }
          this.updateForm ();
      },

      AddProduct() {
         if (this.selectedProduct!=null) {
           this.addedProducts.push(this.selectedProduct);
           this.updateForm ();
           if (this.param.width==0) this.param.width=this.products[this.addedProducts].width
           if (this.param.length==0) this.param.length=this.products[this.addedProducts].length
         }
      },

      sum () {
        var sum=0;
        this.addedProducts.forEach((item)=>{
          sum+=Number.parseFloat (this.products[item].cost);
        })
        return sum;
      },

      updateForm () {
        this.param.cost=this.sum();
        this.param.orderSum=this.sum();
        this.SetParam()
      },

      SetParam() {
        var h=0;
        var w=0;
        this.addedProducts.forEach((item)=>{
          h+=Number.parseFloat (this.products[item].height);
          w+=Number.parseFloat (this.products[item].weight);
        })
        this.param.height=h
        this.param.weight=w
      },

      GetProduct (id) {
        for (var i=0; i<products; i++) {
          if (products[i].id==id) return products[i];
        }
        return null
      },


      GetPrices() {
        this.loading=true
        var data = this.param;
        this.param.endCity=this.param.endCity.charAt(0).toUpperCase() + this.param.endCity.slice(1);
          var region = this.arrayRegion.regions.filter((item)=> {
            return this.selectedRegion.toLowerCase().indexOf(item.name.toLowerCase())==0;
          });
          if (region.length>0) {
            this.param.region.name=region[0].name;
            this.param.region.regionCode=region[0].code;
          } else {
            this.param.region.name=this.selectedRegion;
          }
        var url = "/calculate_backend.php"
        this.$http.get(url,{params : {data}}).then(response => {
          var result = response.data;
          this.test=result;
          this.loading=false
          this.info=this.BuildArray(result);
        }, response => {
          this.loading=false
        });
      },

      BuildArray(array) {
        var cdek = array.result;
        var toArray = {kur:{}, pvz:{}};

          toArray.kur=array.kur;
          toArray.pvz=array.pvz;
          toArray.post=array.post;
        
        var data={};
        var tariff;
        if (cdek!=undefined) {
          cdek.forEach((item)=>{
              tariff=this.CdekTariffText(item.tariffId);
              if (item.status) {
                  data = {
                    name:"СДЭК",
                    typedelivery: tariff.typedelivery,
                    price:item.result.price,
                    delivery:item.result.deliveryPeriodMin+"-"+item.result.deliveryPeriodMax+" дн",
                    tariff:tariff.name,
                    error:""
                  };
              } else {
                  data = {
                  name:"СДЭК",
                  typedelivery: tariff.typedelivery,
                  price:"-",
                  delivery:"-",
                  tariff:tariff.name +"-"+ "Услуга не оказывается",
                  error:""
                };
              }
              if (item.tariffId==137 || item.tariffId==233) toArray.kur.push(data);
              else toArray.pvz.push(data);
          })
        }
        else {
          toArray.pvz.push(this.CdekFiled ());
          toArray.kur.push(this.CdekFiled ());
        }
        toArray.pvz.push(this.SkladNaOgorodnom ());
      return toArray;
      },

      CdekFiled () {
        var data={};
          data = {
          name:"СДЭК",
          price:"-",
          delivery:"-",
          tariff:"Не удалось получить стоимость",
          error:""}
          return data;
      },

      CdekTariffText (tariffId) {
        var tariff ="";
        var typedelivery=0;
        if (tariffId==136)
          tariff="Посылка (склад-склад)";
        if (tariffId==137) {
          typedelivery=1;
          tariff="Посылка (склад-дверь)";
        }
        if (tariffId==233) {
          typedelivery=1;
          tariff="Экономичная посылка (склад-дверь)";
        }
        if (tariffId==234)
          tariff="Экономичная посылка (склад-склад)";
        return {name:tariff, typedelivery:typedelivery};
      },

      SkladNaOgorodnom () {
        var mass = this.param.width*this.param.height*this.param.length/5000;
        var count = this.addedProducts.length;
        var tariff = 150;
        var weight = this.param.weight;
       // if (this.param.weight>mass) mass=this.param.weight;
        mass=Math.round(weight+0.1);
        var price=tariff+50*count+(mass-1)*15;
        tariff = "ПВЗ"
        if (this.param.endCity!="Москва")
          {
            tariff = "Услуга не оказывается"
            price = "-"
          }
        var data = {
          name:"Склад на Огородном",
          typedelivery: 0,
          price:price,
          delivery:"-",
          tariff:tariff,
          error:""
        }
        return data;
      },

      GetType(type) {
        if (type==="pvz") return "Самовывоз"
        else if (type==="post") return "Почта"
        return "Курьер"
      },

      TariffText (item) {
        if (item['error']!="") return item['errormsg'];
        return item['tariff'];
      },

      SetSum(item) {
        var cost = this.param.cost;
        var orderSum = this.param.orderSum;
        var perc = 1.5;
        var price = Number.parseFloat(item.price);
        var sum=0;
        if (item.price>0) {
          
          if (item.name=="СДЭК" || item.name=="Boxberry") {
            perc = 1;
            if (cost>0) perc = 4;
            return this.Raschet (orderSum, cost, price, perc, item.typedelivery);
          }
          if (item.name=="Склад на Огородном") {
            perc = 1.5;
            var d = (orderSum+item.price)*perc/100;
            sum=(d+cost+item.price);
            return Math.round(sum*100)/100;
          }
          if (item.name=="Dalli-service") {
            perc = 4;
            return this.Raschet (orderSum, cost, price, perc, item.typedelivery);
          }


          if (item.name=="Почта" || item.name=="Почта EMS") {
            perc = 3.4;
            if (cost>0) perc += 3;
            var d = orderSum*perc/100;
            sum=(cost+d+price);
            return Math.round(sum*100)/100;
          }

        } return "-";
      },


      Raschet (orderSum, cost, price, perc, typedelivery) {
          var d = (orderSum+price)*perc/100;
          var sum=(d+cost+price);
          var noPerc = this.NormSum(price, typedelivery)
          if (sum<noPerc) sum=noPerc;
          return Math.round(sum*100)/100;
      },


      NormSum(price, typedelivery) {
        var minPVZ = 450;
        var minKUR = 550;
        var minMoskPVZ = 350;
        var minMoskKUR = 450;
        var cost = this.param.cost;
        var noPerc=0;
         if (typedelivery==0) {
            if (this.param.endCity.trim().toLowerCase()=="москва") {
              if (price<minMoskPVZ) noPerc=cost+minMoskPVZ;
            }
            else noPerc=cost+minPVZ;
          }
        if (typedelivery==1) {
            if (this.param.endCity.trim().toLowerCase()=="москва") {
              if (price<minMoskKUR) noPerc=cost+minMoskKUR;
            }
            else noPerc=cost+minKUR;
        }
        return noPerc;
      },

      SetPrice(item) {
        if (item.price>0) {
         var result = this.SetSum(item) - this.param.cost;
         result = Math.round(result*100)/100;
         return result;
        }
        else return "-"
      },

       CheckSum(price) {
         return (price>=600)
       },

       CheckDelivery(delivery) {
         var expl = delivery.split(' ');
         if (expl.length>0) {
           var d = Number.parseInt(expl[0]);
           if (d>6) return true;
         }
         return false
       },

      loadList() {
       var products=null;
        this.$http.get("/products.txt").then(response => {
        var list = response.data;
        this.buildList(list);
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
                    name:line[0],
                    cost:line[1],
                    nameCost:line[0]+" за "+line[1],
                    weight:line[2],
                    length:p[0],
                    width:p[1],
                    height:p[2]
                } 
                this.products.push(params);
            }
        }
    }, 

    getArray (array) {
      var r=[];
      var o;
      array.forEach((item)=>{
        o[key1]=item[key1];
        o[key2]=item[key2];
        r.push(0);
      })
      return r.sort();
    }

    },
  computed :
  {
      buttonDisble () {
        var params = this.param;
      if (this.selectedRegion!='') return false;
      else return true;
    }
  },
  mounted() {
      this.$http.get("cityList.txt").then(response => {
        this.arrayRegion = response.data;
        this.regionList = this.arrayRegion.regions;
        this.cityList = this.arrayRegion.city;
        }, response => {
      });

      this.loadList();
   }
}
</script>

<style lang="sass" scoped>

.red 
  color: red
  font-weight: bold

.form 
  max-width: 700px
  margin: 0 auto
  margin-bottom: 20px
  border-bottom: 1px solid #E2E2E2
  padding-bottom: 20px
  .selectList
    margin-bottom: 10px
    select 
      border: 1px solid #E2E2E2
  .product_line
    font-size: 14px
    display: flex
    justify-content: space-between
    align-items: center
    margin-bottom: 10px
    .input-cost
      display: flex
    .cost
      width: 50px
      height: 25px
      border-radius: 3px
      border: 1px solid #E2E2E2
      margin-left: 15px
      text-align: center
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button 
      -webkit-appearance: none
    
    input[type='number'] 
      -moz-appearance: textfield
  
    
  .input_line
    margin-bottom: 20px
    display: flex
    div 
        width: 100%
        margin-right: 10px
    input
      padding-left: 5px
      width: 100%
      height: 25px
      border-radius: 2px
      border: 1px solid #E2E2E2
.result 
    margin-top: 15px
    font-size: 12px
    transition: .9sec all
    &-row
        display: flex
        justify-content: center   
        border: 1px solid #E2E2E2
        margin-bottom: 20px
    &-type
      width: 100px
      display: flex
      align-items: center
      padding-left: 15px
      border-right: 1px solid #E2E2E2
      font-size: 16px
      font-weight: bold
    &-data 
      flex-grow: 1
    &-items
        display: flex
        border-bottom: 1px solid #E2E2E2
        &:last-child
          border-bottom: 0px 
        .item
            padding: 10px 0px 10px 10px
            display: flex
            align-items: center
            box-sizing: border-box
            border-right: 1px solid #E2E2E2
            &:last-child
              border-right: 0px 
        .name
          width: 25%
        .price
          width: 12%
        .delivery
          width: 15%
        .tariff
          width: 33%
        .sum
          width: 15%
        .sum,.delivery,.price
          padding: 10px 0px 10px 10px
          
</style>