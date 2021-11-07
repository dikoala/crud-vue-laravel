<template>
    <div id="tgroup">
        <div id="quote">{{quote}}</div>
        <input type="text"
         placeholder="search"
         v-model="filter" />
        <table id="articles">
            <thead>
                <th :class="sortedClass('id')" @click="sortBy('id')">ID</th>
                <th :class="sortedClass('title')" @click="sortBy('title')">Title</th>
                <th :class="sortedClass('slug')" @click="sortBy('slug')">URL</th>
                <th :class="sortedClass('created')" @click="sortBy('created')">Date Created</th>
                <th :class="sortedClass('updated')" @click="sortBy('updated')">Date updated</th>
                <th :class="sortedClass('status')" @click="sortBy('status')">Published</th>
                <th colspan="2">Action</th>
            </thead>

            <tbody>
                <tr v-for="(article, index) in sortedItems" :key="article.id">
                <td>{{ article.id }}</td>
                <td><a href="#" @click="edit(article.id)">{{ article.title }}</a></td>
                <td><a href="#" :data-slug="`${article.slug}`" @click="callFunction($event, article.status)">{{ '/articles/' + article.slug }}</a></td>
                <td>{{ article.created }}</td>
                <td>{{ article.updated }}</td>
                <td><input type="checkbox" :checked="article.status == 'Published'" ></td>
                <td><button @click="edit(article.id)">Edit</button></td>
                <td><button @click="deleteItem(article.id, index)">Delete</button></td>
                </tr>

            </tbody>
        </table>
        <div>
            <button class="button" @click="prev" :class="{disabled: prevpage==null}">Previous</button>
            <button class="button" @click="next" :class="{disabled: nextpage==null}">Next</button>
            <div class="entries">
                <label for="cars">Choose entries:</label>
                <input type="number" v-model="entries" @change="setEntry" min="1">
            </div>
        </div>
    </div>
</template>
<script>
export default{
    name: 'Articles',
    data(){
        return{
            articles: [],
            quotes: [],
            quote: '',
            entries: 20,
            nextpage: null,
            prevpage: null,
            filter: "",
            sort: {
                key: '',
                isAsc: false
            }
        }
    },
    methods:{
        callFunction: function (event, arg) {
            var slug = event.target.getAttribute('data-slug');
            if (arg == 'Published') {
                this.$router.push({
                    name: "Article",
                    params: { slug }
                });
            } else {
                this.$router.push({
                    name: "Page404"
                });
            }

            console.log(arg)
        },
        edit(id) {
            this.$router.push({
                name: "Edit",
                params: { id }
            });
        },
        deleteItem(id, index) {
            fetch('http://localhost:8000/api/article/' + id, {
                method: 'delete',
            })
            .then(res => res.text()) // or res.json()
            .then(res => {
                console.log(res)
                document.getElementById("articles").deleteRow(index);
            })
        },
        next() {
            if (this.nextpage !== null) {
                fetch(this.nextpage)
                .then(response => response.json())
                .then(data => {
                    this.articles = data.data
                    console.log(data.data)
                    this.nextpage = data.links.next
                    this.prevpage = data.links.prev
                    console.log(this.nextpage, this.prevpage)
                });
            }
        },
        prev() {
            if (this.prevpage !== null) {
                fetch(this.prevpage)
                .then(response => response.json())
                .then(data => {
                    this.articles = data.data
                    console.log(data.data)
                    this.nextpage = data.links.next
                    this.prevpage = data.links.prev
                    console.log(this.nextpage, this.prevpage)
                });
            }
        },
        setEntry() {
            this.entries = event.target.value
            fetch('http://localhost:8000/api/articles/' + this.entries)
            .then(response => response.json())
            .then(data => {
                this.articles = data.data
                this.nextpage = data.links.next
                this.prevpage = data.links.prev
            });
        },
        sortedClass (key) {
            return this.sort.key === key ? `sorted ${this.sort.isAsc ? 'asc' : 'desc' }` : '';
        },
        sortBy (key) {
            this.sort.isAsc = this.sort.key === key ? !this.sort.isAsc : false;
            this.sort.key = key;
        },
        getQuotes: function () {
            setInterval(() => {
                let idx = Math.floor(Math.random() * this.quotes.length)
                this.quote = this.quotes[idx].text + ' - ' + ((this.quotes[idx].author == null) ? 'Anonymous' : this.quotes[idx].author);
            }, 10000);

        }

    },
    created(){
        fetch('http://localhost:8000/api/articles/' + this.entries)
            .then(response => response.json())
            .then(data => {
                this.articles = data.data
                console.log(data.data)
                this.nextpage = data.links.next
                this.prevpage = data.links.prev
                console.log(this.nextpage, this.prevpage)
        });

        fetch('https://type.fit/api/quotes')
            .then(response => response.json())
            .then(data => {
                this.quotes = data
        });

        this.getQuotes()

    },
    computed: {
        sortedItems () {
            const list = this.articles.slice();
            if (this.sort.key) {
                list.sort((a, b) => {
                    a = a[this.sort.key]
                    b = b[this.sort.key]

                    return (a === b ? 0 : a > b ? 1 : -1) * (this.sort.isAsc ? 1 : -1)
                });
            }

            if (this.sort.key) {
                return list;
            } else {
               return this.articles.filter(row => {
                    const titles = row.title.toString().toLowerCase();
                    const created = row.created.toLowerCase();
                    const searchTerm = this.filter.toLowerCase();

                    return titles.includes(searchTerm) || created.includes(searchTerm);
                });
            }

        }
    },
}
</script>
<style scoped>
#tgroup {
    width: 50%;
    margin-top: 20px;
    height: auto;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding-top: 10px;
    padding-bottom: 10px;
}
#articles {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#articles td, #articles th {
  border: 1px solid #ddd;
  padding: 8px;
}

#articles tr:nth-child(even){background-color: #f2f2f2;}

#articles tr:hover {background-color: #ddd;}

#articles th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}

.button {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 4px;
  cursor: pointer;
}

.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.entries {
    display: inline;
}

table th.sorted.asc::after {
    display: inline-block;
    content: '▼';
}
table th.sorted.desc::after {
    display: inline-block;
    content: '▲';
}

input[type=text], select {
  width: 25%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

#quote {
    margin-top: 10px;
    height: 50px;
}

th:not(:last-child) {
    cursor: pointer;
}
</style>
