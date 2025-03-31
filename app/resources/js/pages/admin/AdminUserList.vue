<template>
  <div class="container mt-5">
    <div class="mb-3">
      <button class="btn btn-outline-secondary" @click="goBack">← 戻る</button>
    </div>

    <h3 class="mb-4">ユーザー一覧</h3>

    <div v-if="loading">読み込み中...</div>

    <div v-else>
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>名前</th>
            <th>メール</th>
            <th>電話番号</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.phone }}</td>
            <td>
              <router-link
                :to="`/admin/users/${user.id}`"
                class="btn btn-sm btn-outline-primary"
              >
                詳細
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>
  </div>
</template>

<script>
export default {
  name: 'AdminUserList',
  data() {
    return {
      users: [],
      loading: true,
      message: ''
    }
  },
  async created() {
    await this.getUsers()
  },
  methods: {
    // ユーザー一覧を取得する
    async getUsers() {
      try {
        const res = await fetch('/api/admin/users', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
          }
        })
        const data = await res.json()
        if (res.ok) {
          this.users = data.users
        } else {
          this.message = data.error || '取得に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました。'
      } finally {
        this.loading = false
      }
    },
    // 戻るボタン処理
    goBack() {
      this.$router.push('/admin')
    }
  }
}
</script>
