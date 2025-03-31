<template>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>管理者ダッシュボード</h2>
      <LogOut />
    </div>

    <p>ユーザー管理・ガイド申請管理</p>

    <div class="d-grid gap-3 mt-4">
      <router-link to="/admin/users" class="btn btn-outline-primary btn-lg">
        ユーザー管理
      </router-link>
      <router-link to="/admin/guide-requests" class="btn btn-outline-secondary btn-lg">
        ガイド申請管理
      </router-link>
    </div>
  </div>
</template>

<script>
import LogOut from '@/components/LogOut.vue'

export default {
  name: 'AdminDashboard',
  components: {
    LogOut
  }
}
</script>
