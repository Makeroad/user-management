import { createRouter, createWebHistory } from 'vue-router'
import LoginRegister from '@/components/LoginRegister.vue'
import MyPage from '@/pages/user/MyPage.vue'
import GuideApply from '@/pages/guide/GuideApply.vue'
import AdminDashboard from '@/pages/admin/AdminDashboard.vue'
import AdminUserList from '@/pages/admin/AdminUserList.vue'
import AdminUserDetail from '@/pages/admin/AdminUserDetail.vue'
import AdminGuideList from '@/pages/admin/AdminGuideList.vue'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: LoginRegister },
  { path: '/mypage', component: MyPage },
  { path: '/mypage/guide', name: 'GuideApply',component: GuideApply},
  { path: '/admin', component: AdminDashboard },
  { path: '/admin/users', component: AdminUserList },
  { path: '/admin/users/:id', component: AdminUserDetail, props: true },
  { path: '/admin/guide-requests', component: AdminGuideList },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ページ移動の前に毎回チェックする
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const savedUser = localStorage.getItem('user')
  const user = savedUser ? JSON.parse(savedUser) : null

  // すでにログインしているのに /login にアクセスしたらリダイレクト
  if (to.path === '/login' && token && user) {
    if (user.role === 3) {
      next('/admin')  // 管理者なら管理者ページへ
    } else {
      next('/mypage') // 普通のユーザーならマイページへ
    }
    return
  }

  // ログインしてないのに色んなページに行こうとするのを制限する
  const paths = ['/mypage', '/mypage/guide', '/admin', '/admin/users', '/admin/guide-requests']
  const limitPath = paths.some(path => to.path.startsWith(path))
  if (limitPath && !token) {
    next('/login')
    return
  }

  // 管理者ページは管理者のみ
  if (to.path.startsWith('/admin') && (!user || user.role !== 3)) {
    next('/login')
    return
  }

  next()
})


export default router
