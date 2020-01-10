<template>
<div class="app">
    <div class="container">
    <product v-for="(c, index) in componentsArray" :index="index"></product> 
  </div>
</div>
</template>

<script>
import product from './assets/components/product'
export default {

   components: {
    product
  },

  data () {
    return {
      componentsArray: this.$store.state.calculator,
    }
  },
  methods: {
    addForm () {
          this.$store.state.calculator.push({id:this.$store.state.calculator.length});
    }
  },
}
</script>

<style lang="sass">

  h1
    font-family: sans-serif
    font-size: 20px
  .container 
    max-width: 1170px
    margin: 0 auto

  .selectList
    max-width: 700px
    margin: 0 auto
    select
      height: 30px

  .button
    display: flex
    justify-content: space-between


  button
    background-color: #80D519
    border: 0px
    padding: 0 10px
    min-width: 35px
    height: 35px
    border-radius: 3px
    color: #fff

  .close-btn
    width: 28px
    height: 28px
    padding: 0
    display: inline-block
    background-color: #FF5050
    color: #fff
    border-radius: 3px
    border: 1px solid #E2E2E2
    color: #fff
    display: flex
    justify-content: center
    align-items: center
    margin-left: 5px
    cursor: pointer

</style>
