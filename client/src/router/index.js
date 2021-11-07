import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Articles from '../views/Articles.vue'
import Article from '../views/Article.vue'
import Notfound from '../views/Page404.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/articles',
    name: 'Articles',
    component: Articles
  },
  {
    path: '/articles/:slug',
    name: 'Article',
    component: Article,
    props: true
  },
  {
    path: '/edit/:id',
    name: 'Edit',
    component: Home,
    props: true
  },
  {
    path: '/404',
    name: 'Page404',
    component: Notfound
  },

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
