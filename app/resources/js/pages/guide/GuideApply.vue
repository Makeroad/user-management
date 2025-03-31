<template>
  <div class="container mt-5">
    <h3 class="mb-4">ガイド申請</h3>

    <div v-if="loading" class="mb-4">読み込み中...</div>

    <div v-else>
      <!-- すでにガイド承認済みの場合 -->
      <div v-if="status === 'approved'" class="alert alert-success">
        あなたはすでにガイドとして承認されています。
        <div class="mt-3 text-center">
          <button class="btn btn-outline-secondary" @click="goBack">← マイページへ戻る</button>
        </div>
      </div>

      <!-- 審査中の場合 -->
      <div v-else-if="status === 'pending'" class="alert alert-info">
        現在、申請中です。審査結果をお待ちください。
        <div class="mt-3 text-center">
          <button class="btn btn-outline-secondary" @click="goBack">← マイページへ戻る</button>
        </div>
      </div>

      <!-- 新規申請フォーム -->
      <div v-else>
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label class="form-label">タイトル</label>
            <input v-model="form.title" type="text" class="form-control" required />
          </div>

          <div class="mb-3">
            <label class="form-label">自己紹介・説明</label>
            <textarea v-model="form.description" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">対応言語</label>
            <input v-model="form.languages" type="text" class="form-control" placeholder="例: 日本語、英語" required />
          </div>

          <div class="mb-3">
            <label class="form-label">案内地域</label>
            <input v-model="form.region" type="text" class="form-control" required />
          </div>

          <div class="mb-3">
            <label class="form-label">ガイド経験年数（任意）</label>
            <input v-model="form.experience_years" type="number" class="form-control" min="0" />
          </div>

          <button type="submit" class="btn btn-primary w-100">申請する</button>
        </form>

        <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>

        <div class="text-center mt-4">
          <button class="btn btn-outline-secondary" @click="goBack">← マイページへ戻る</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GuideApply',
  data() {
    return {
      status: null, // 申請か拒否か申請中か
      loading: true,
      message: '',
      form: {
        title: '',
        description: '',
        languages: '',
        region: '',
        experience_years: null
      }
    }
  },
  async created() {
    // ガイド申請ステータス取得
    try {
      const res = await fetch('/api/mypage/guide/status', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('token')
        }
      })
      const data = await res.json()
      if (res.ok) {
        this.status = data.status
      } else {
        this.message = data.error || 'ステータス取得に失敗しました。'
      }
    } catch {
      this.message = '通信エラーが発生しました。'
    } finally {
      this.loading = false
    }
  },
  methods: {
    // マイページへ戻る
    goBack() {
      this.$router.push('/mypage')
    },

    // ガイド申請送信
    async submitForm() {
      try {
        const res = await fetch('/api/mypage/guide/apply', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token')
          },
          body: JSON.stringify(this.form)
        })
        const data = await res.json()
        if (res.ok) {
          alert(data.message || '申請が完了しました。')
          this.status = 'pending'
        } else {
          this.message = data.error || '申請に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました。'
      }
    }
  }
}
</script>
