<template>
    <div>
      <nav class="navbar navbar-light bg-light justify-content-between px-3">
        <span class="navbar-brand mb-0 h1">ユーザー管理システム課題</span>
  
        <div v-if="user">
          <span class="me-3">{{ user.name }} さん</span>
          <button class="btn btn-outline-secondary btn-sm" @click="logout">ログアウト</button>
        </div>
      </nav>
  
      <main class="p-4">
        <slot></slot>
      </main>
    </div>
  </template>
  
  <script>
  export default {
    name: 'LogOut',
    computed: {
      user() {
        const user = localStorage.getItem('user')
        return user ? JSON.parse(user) : null
      }
    },
    methods: {
      async logout() {
        try {
          const res = await fetch('/api/logout', {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('token')
            }
          })
          if (res.ok) {
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            this.$router.push('/login')
          }
        } catch {
          alert('ログアウトに失敗しました')
        }
      }
    }
  }
  </script>
  