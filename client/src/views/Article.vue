<template>
    <div class="article">
        <div class="card">
            <h2>Article # {{article.id}}</h2>
            <img :src="`http://localhost:8000/storage/uploads/${article.photo}`" alt="Cover Photo" style="width:40%">
            <div class="container">
            <h4><b>{{article.title}}</b></h4>
            <p>{{article.body}}</p>
        </div>
        </div>
    </div>
</template>
<script>
export default{
    data(){
        return{
            article: [],
        }
    },
    methods:{

    },
    created(){
        var pathArray = window.location.pathname.split('/');
        var slug = pathArray[2]
        fetch('http://localhost:8000/api/article/' + slug)
        .then(response => response.json())
        .then(data => {
            this.article = data.data
        });
    }
}
</script>
<style scoped>
.article {
    display: flex;
    justify-content: center;
}
.card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  width: 40%;
}

.card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

.container {
  padding: 2px 16px;
}
</style>
