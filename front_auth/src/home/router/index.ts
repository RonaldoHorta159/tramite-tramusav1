export default {
  name: 'home',
  path: '/home',
  component: () => import('../../components/HomeUser.vue'),
  children: [
    {
      name: 'home-index',
      path: '',
      component: () => import('../views/index.vue'),
    },
    {
      name: 'home-tramite',
      path: 'tramite',
      component: () => import('../views/tramite/view/Tramite.vue'),
    },
    {
      name: 'home-recibir',
      path: 'recibir',
      component: () => import('../views/recibir/view/Recibir.vue'),
    },
    // Si tienes más vistas hijas, dales igualmente un prefijo 'home-'
    // { name: 'home-documentos', path: 'documentos', component: () => import('../views/Documentos.vue') },
    // { name: 'home-usuarios', path: 'usuarios', component: () => import('../views/Usuarios.vue') },
  ],
}
