<template>
    <div>
        <h3>Create Article</h3>
        <div class="alert">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            <strong>Whoops!</strong> There were some problems with your input.
            <ul id="list"></ul>

        </div>
        <div class="container">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Your title" v-model="formData.title">

                <label for="content">Content</label>
                <textarea id="body" name="body" placeholder="Write something.." style="height:200px" v-model="formData.body"></textarea>

                <input type="file" id="photo" name="photo" accept=".png, .jpg" @change=uploadImage>

                <label for="content">Tags (optional)</label>
                <TagInput v-model="tags" /><br /><br />

                <input type="button" @click="submit('Published')" class="publish" value="Publish">
                <input type="button" @click="submit('Draft')" class="draft" value="Save as Draft">
        </div>
    </div>
</template>
<script>
import TagInput from "../components/TagInput.vue";

export default{
    name: 'Home',
    components: {
        TagInput: TagInput,
    },
    data(){
        return {
            formData: {
                title: '',
                body: '',
                cover_photo: '',
            },
            errors: {},
            tags: [],
            id: null,
        }
    },
    methods:{
        submit(value){
            var method = 'put'
            this.formData.status = value
            if (this.tags.length) {
                this.formData.tags = this.tags.toString()
            }
            if (this.id == null) {
                method = 'post'
            } else {
                this.formData.article_id = this.id
            }
            fetch('http://localhost:8000/api/article', {
            method: method,
            headers: {
                'Accept': 'application/json, text/plain, */*',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(this.formData)
            })
            .then(res => res.json())
            .then(res => {
                var ul = document.getElementById("list");
                var li = document.createElement("li");
                ul.innerHTML = '';
                console.log(res.errors)
                if ( typeof res.errors !== 'undefined' ) {
                    if ( typeof res.errors.title !== 'undefined' ) {
                        li.appendChild(document.createTextNode(res.errors.title[0]));
                        ul.appendChild(li);
                    }
                    if ( typeof res.errors.body !== 'undefined' ) {
                        li = document.createElement("li");
                        li.appendChild(document.createTextNode(res.errors.body[0]));
                        ul.appendChild(li);
                    }
                    if ( typeof res.errors.cover_photo !== 'undefined' ) {
                        li = document.createElement("li");
                        li.appendChild(document.createTextNode(res.errors.cover_photo[0]));
                        ul.appendChild(li);
                    }
                    var ule = document.getElementsByClassName('alert')
                    ule[0].style.display = "block";
                } else {
                    this.$router.replace({ name: 'Articles' })
                }
            })
        },
        uploadImage(e){
            const image = e.target.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onload = e =>{
                this.formData.cover_photo = e.target.result;
            };
        }
    },
    created(){
        var pathArray = window.location.pathname.split('/');
        var id = pathArray[2]
        if (typeof(id) != "undefined") {
            fetch('http://localhost:8000/api/article/' + id)
            .then(response => response.json())
            .then(data => {
                this.formData = data.data
                this.id = this.formData.id
                if (this.formData.tags !==null) {
                    const stale = this.formData.tags.split(",")
                    this.tags = this.tags.push(...stale)
                }
            });
        }
    }
}
</script>
<style scoped>

input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
  resize: vertical;
}

input[type=button] {
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.draft {
    background-color: #DC3545;
}

.publish {
    background-color: #007BFF;
    margin-right: 10px;
}

input[type=button]:hover {
  background-color: #6C757D;
}

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
  text-align: left;
}

#photo {
    display: block;
    margin-bottom: 20px;
}

.alert {
  text-align: left;
  padding: 20px;
  margin-left: 20px;
  margin-right: 20px;
  background-color: #f8d7da;
  color: #721c24;
  display: none;
}

.closebtn {
  margin-left: 15px;
  color: #721c24;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

.closebtn:hover {
  color: black;
}
</style>
