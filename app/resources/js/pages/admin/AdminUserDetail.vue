<template>
  <div class="container mt-5">
    <div class="mb-3">
      <button class="btn btn-outline-secondary" @click="goBack">← 戻る</button>
    </div>

    <h3>ユーザー詳細（ID: {{ userId }}）</h3>

    <div v-if="loading">読み込み中...</div>

    <div v-else-if="user">
      <form @submit.prevent="updateUser">
        <div class="mb-3">
          <label class="form-label">名前</label>
          <input v-model="form.name" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">電話番号</label>
          <input v-model="form.phone" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">郵便番号</label>
          <input v-model="form.postal_code" type="text" class="form-control" />
        </div>

        <div class="mb-3">
          <label class="form-label">住所</label>
          <input v-model="form.address" type="text" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100">更新する</button>
        <button type="button" class="btn btn-danger w-100 mt-2" @click="deleteUser">ユーザー削除</button>
      </form>

      <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>
    </div>

    <div v-else class="alert alert-warning">ユーザー情報が見つかりません。</div>
  </div>
</template>

<script>
export default {
  name: 'AdminUserDetail',
  props: ['id'],
  data() {
    return {
      userId: this.id,
      user: null,
      form: {
        name: '',
        phone: '',
        postal_code: '',
        address: '',
        email: '',
      },
      loading: true,
      message: ''
    }
  },
  async created() {
    await this.getUserDetail()
  },
  methods: {
    goBack() {
      this.$router.push('/admin/users')
    },
    async getUserDetail() {
      try {
        const res = await fetch(`/api/admin/users/${this.userId}`, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
          }
        })

        const data = await res.json()

        if (res.ok) {
          this.user = data.user
          this.form = {
            name: data.user.name,
            phone: data.user.phone,
            postal_code: data.user.postal_code,
            address: data.user.address,
            email: data.user.email,
          }
        } else {
          this.message = data.error || '取得に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました。'
      } finally {
        this.loading = false
      }
    },
    async updateUser() {
      try {
        const res = await fetch(`/api/admin/users/${this.userId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token')
          },
          body: JSON.stringify(this.form)
        })
        const data = await res.json()
        if (res.ok) {
          this.message = 'ユーザー情報を更新しました。'
        } else {
          this.message = data.error || '更新に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました。'
      }
    },
    async deleteUser() {
      if (!confirm('本当にこのユーザーを削除しますか？')) return
      try {
        const res = await fetch(`/api/admin/users/${this.userId}`, {
          method: 'DELETE',
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
          }
        })
        const data = await res.json()
        if (res.ok) {
          alert('ユーザーを削除しました。')
          this.$router.push('/admin/users')
        } else {
          this.message = data.error || '削除に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました。'
      }
    }
  }
}
</script>
