<template>
    <ul class="topnav">
        <li><router-link :to="{name:'Home'}"> Home</router-link></li>
        <li><router-link :to="{name:'Articles'}"> Articles</router-link></li>
    </ul>
</template>
<script>
export default{
    data(){
        return{

        }
    },
    methods:{
    },
    created(){

    }
}
</script>
<style scoped>
.router-link-exact-active {
  background-color: #04AA6D;
  color: white;
}

ul.topnav {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

ul.topnav li {float: left;}

ul.topnav li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}
</style>
