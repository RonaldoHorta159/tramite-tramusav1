import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../components/LoginUser.vue'
import RegisterView from '@/views/RegisterView.vue'
import routerHome from '../home/router'

const routes = [
  {
    path: '/',
    component: LoginView,
  },
  {
    path: '/Register',
    component: RegisterView,
  },
  {
    ...routerHome,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
