<template>
  <div class="container mt-5">
    <div class="mb-3">
      <button class="btn btn-outline-secondary" @click="goBack">← 戻る</button>
    </div>
    
    <h2>ガイド申請一覧</h2>

    <!-- ローディング中の表示 -->
    <div v-if="loading" class="my-4">読み込み中...</div>

    <!-- データが空の場合の表示 -->
    <div v-else-if="guideRequests.length === 0" class="alert alert-info">
      現在、審査待ちの申請はありません。
    </div>

    <!-- 申請データの表示 -->
    <div v-else>
      <div v-for="request in guideRequests" :key="request.request_id" class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">{{ request.name }} ({{ request.email }})</h5>
          <p class="card-text">
            <strong>タイトル:</strong> {{ request.title }}<br>
            <strong>地域:</strong> {{ request.region }}<br>
            <strong>対応言語:</strong> {{ request.languages }}<br>
            <strong>経験年数:</strong> {{ request.experience_years || '不明' }}<br>
            <strong>自己紹介:</strong> {{ request.description }}
          </p>
          <div class="mt-3">
            <button class="btn btn-success me-2" @click="approveRequest(request.request_id)">承認</button>
            <button class="btn btn-danger" @click="rejectRequest(request.request_id)">拒否</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="message" class="alert alert-info mt-4">{{ message }}</div>
  </div>
</template>

<script>
export default {
  name: 'AdminGuideList',
  data() {//初期化
    return {
      guideRequests: [], // ガイド申請データ
      loading: true,     
      message: ''        // メッセージ
    }
  },
  async created() {
    // 初期データ取得
    await this.fetchGuideRequests()
  },
  methods: {
    // ガイド申請一覧を取得
    async fetchGuideRequests() {
      this.loading = true
      this.message = ''
      try {
        const res = await fetch('/api/admin/guide-requests', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
          }
        })
        const data = await res.json()

        if (res.ok) {
          this.guideRequests = data.requests
        } else {
          this.message = data.error || '読み込みに失敗しました'
        }
      } catch {
        this.message = '通信エラーが発生しました'
      } finally {
        this.loading = false
      }
    },

    // 申請を承認する
    async approveRequest(id) {
      if (confirm('この申請を承認しますか？')) {
        await this.sendDecision(id, 'approve')
      }
    },

    // 申請を拒否する
    async rejectRequest(id) {
      if (confirm('この申請を拒否しますか？')) {
        await this.sendDecision(id, 'reject')
      }
    },

    // 承認・拒否のリクエストを送信
    async sendDecision(id, action) {
      this.message = ''
      try {
        const res = await fetch(`/api/admin/guide-requests/${id}/${action}`, {
          method: 'POST',
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
          }
        })
        const data = await res.json()

        if (res.ok) {
          this.message = data.message || '処理が完了しました'
          await this.fetchGuideRequests() // 一覧を更新
        } else {
          this.message = data.error || '処理に失敗しました'
        }
      } catch {
        this.message = '通信エラーが発生しました'
      }
    },
    // 戻るボタン処理
    goBack() {
      this.$router.push('/admin')
    }
  }
}
</script>