<template>
  <div class="card p-4 shadow-sm" style="max-width: 400px; margin: auto;">
    <h4 class="text-center mb-4">{{ isLogin ? 'ログイン' : '新規登録' }}</h4>

    <!-- ログインまたは新規登録フォーム -->
    <form @submit.prevent="handleSubmit" v-if="!registeringCode">
      <div class="mb-3">
        <label class="form-label">メールアドレス</label>
        <input v-model="email" type="email" class="form-control" required />
      </div>

      <div class="mb-3">
        <label class="form-label">パスワード</label>
        <input v-model="password" type="password" class="form-control" required />
      </div>

      <!-- 新規登録フォームのみ表示 -->
      <div v-if="!isLogin">
        <div class="mb-3">
          <label class="form-label">名前</label>
          <input v-model="name" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">電話番号</label>
          <input v-model="phone" type="text" class="form-control" required />
        </div>
        <!-- 郵便局APIとか使えそうならそっちのデータを引っ張って表示する -->
        <div class="mb-3">
          <label class="form-label">郵便番号</label>
          <input v-model="postal_code" type="text" class="form-control" />
        </div>
        <!-- 郵便局APIとか使えそうならそっちのデータを引っ張って表示する -->
        <div class="mb-3">
          <label class="form-label">住所</label>
          <input v-model="address" type="text" class="form-control" required />
        </div>
      </div>

      <button class="btn btn-primary w-100" type="submit">
        {{ isLogin ? 'ログイン' : '登録' }}
      </button>
    </form>

    <!-- 認証コード入力フォーム -->
    <form v-else @submit.prevent="verifyEmail" class="mt-3">
      <p class="text-center">メールに送信された認証コードを入力してください</p>
      <div class="mb-3">
        <label class="form-label">認証コード</label>
        <input v-model="verificationCode" type="text" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-success w-100">認証して登録完了</button>
    </form>

    <!-- フォーム切り替えボタン -->
    <button class="btn btn-link mt-3 w-100" @click="toggleForm">
      {{ isLogin ? 'アカウントがありませんか？登録はこちら' : 'ログインはこちら' }}
    </button>

    <!-- メッセージ表示 -->
    <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>
  </div>
</template>

<script>
export default {
  name: 'LoginRegister',
  data() {
    return {
      isLogin: true, // ログイン/新規登録の切り替え
      registeringCode: false, // 認証コード入力モード
      email: '',
      password: '',
      name: '',
      phone: '',
      postal_code: '',
      address: '',
      verificationCode: '',
      message: '',
    }
  },
  methods: {
    // ログイン・登録フォームの切り替え
    toggleForm() {
      this.isLogin = !this.isLogin
      this.registeringCode = false
      this.clearForm()
    },
    // フォーム内容初期化
    clearForm() {
      this.email = ''
      this.password = ''
      this.name = ''
      this.phone = ''
      this.postal_code = ''
      this.address = ''
      this.verificationCode = ''
      this.message = ''
    },
    // ログインまたは登録処理
    async handleSubmit() {
      const url = this.isLogin ? '/api/login' : '/api/register'
      const body = this.isLogin
        ? { email: this.email, password: this.password }
        : {
            email: this.email,
            password: this.password,
            name: this.name,
            phone: this.phone,
            postal_code: this.postal_code,
            address: this.address,
          }

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body),
        })
        const data = await res.json()

        if (res.ok) {
          if (this.isLogin && data.token) {
            localStorage.setItem('token', data.token)//localStorageへ保存する
            localStorage.setItem('user', JSON.stringify(data.user))//localStorageへ保存する
            this.$router.push(data.user.role === 3 ? '/admin' : '/mypage')
          } else if (!this.isLogin) {
            this.message = '仮登録完了。メールの認証コードを入力してください。'//一旦会員登録
            this.registeringCode = true
          }
        } else {
          this.message = data.error || '失敗しました'
        }
      } catch {
        this.message = '通信エラーが発生しました'
      }
    },
    // 認証コードを確認して登録完了処理
    async verifyEmail() {
      try {
        const res = await fetch('/api/email/verify', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: this.email,
            code: this.verificationCode,
          }),
        })
        const data = await res.json()
        if (res.ok) {
          this.message = '登録が完了しました！ログインしてください。'
          this.registeringCode = false
          this.toggleForm()
        } else {
          this.message = data.error || '認証に失敗しました。'
        }
      } catch {
        this.message = '通信エラーが発生しました'
      }
    },
  },
}
</script>