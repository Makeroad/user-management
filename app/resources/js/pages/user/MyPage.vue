<template>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>マイページ</h2>
      <LogOut />
    </div>

    <div v-if="user" class="mt-4">
      <!-- 個人情報 -->
      <template v-if="!editMode">
        <p><strong>名前:</strong> {{ user.name }}</p>
        <p><strong>メール:</strong> {{ user.email }}</p>
        <p><strong>電話番号:</strong> {{ user.phone }}</p>
        <p><strong>住所:</strong> {{ user.address }}</p>
        <p><strong>郵便番号:</strong> {{ user.postal_code }}</p>

        <button class="btn btn-outline-primary mt-3" @click="startEdit">編集</button>
      </template>

      <!-- 編集フォーム -->
      <form v-else @submit.prevent="handleUpdate">
        <div class="mb-3">
          <label class="form-label">名前</label>
          <input v-model="form.name" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">電話番号</label>
          <input v-model="form.phone" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">住所</label>
          <input v-model="form.address" type="text" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">郵便番号</label>
          <input v-model="form.postal_code" type="text" class="form-control" />
        </div>

        <button class="btn btn-primary w-100" type="submit">保存</button>
        <button class="btn btn-link mt-2 w-100" type="button" @click="cancelEdit">キャンセル</button>
      </form>

      <!-- <hr class="my-5" />
      <h5>メールアドレス</h5>

      <div v-if="!editEmailMode">
        <p><strong>現在:</strong> {{ user.email }}</p>
        <button class="btn btn-outline-primary btn-sm" @click="editEmailMode = true">メール編集</button>
      </div>

      <div v-else>
        <p><strong>現在:</strong> {{ user.email }}</p>
        <form @submit.prevent="requestEmailChange" class="mb-3" v-if="!emailChangeRequested">
          <div class="mb-2">
            <label class="form-label">新しいメールアドレス</label>
            <input v-model="newEmail" type="email" class="form-control" required />
          </div>
          <button class="btn btn-outline-primary w-100" type="submit">認証コードを送信</button>
        </form>

        <form @submit.prevent="verifyEmailChange" class="mb-3" v-if="emailChangeRequested">
          <div class="mb-2">
            <label class="form-label">認証コード</label>
            <input v-model="verificationCode" type="text" class="form-control" required />
          </div>
          <button class="btn btn-success w-100" type="submit">認証して変更</button>
        </form>

        <button class="btn btn-link w-100 mt-2" @click="cancelEmailEdit">キャンセル</button>
      </div>

      <div v-if="emailMessage" class="alert alert-info mt-3">{{ emailMessage }}</div> -->

      <hr class="my-5" />
      <h5>パスワード変更</h5>

      <div v-if="!showPasswordForm">
        <button class="btn btn-outline-danger" @click="showPasswordForm = true">パスワード変更</button>
      </div>

      <form v-else @submit.prevent="changePassword" class="mt-3">
        <div class="mb-3">
          <label class="form-label">現在のパスワード</label>
          <input v-model="currentPassword" type="password" class="form-control" required />
        </div>

        <div class="mb-3">
          <label class="form-label">新しいパスワード</label>
          <input v-model="newPassword" type="password" class="form-control" required minlength="6" />
        </div>

        <div class="mb-3">
          <label class="form-label">新しいパスワード（確認）</label>
          <input v-model="confirmPassword" type="password" class="form-control" required />
        </div>

        <button class="btn btn-outline-danger w-100" type="submit">パスワードを変更</button>
        <button class="btn btn-link w-100 mt-2" type="button" @click="showPasswordForm = false">キャンセル</button>
      </form>

      <div v-if="passwordMessage" class="alert alert-info mt-3">{{ passwordMessage }}</div>

      <hr class="my-5" />
      <router-link to="/mypage/guide" class="btn btn-outline-success">
        ガイド申請はこちら
      </router-link>

      <div v-if="message" class="alert alert-info mt-3">{{ message }}</div>
    </div>

    <div v-else class="alert alert-warning">ユーザー情報が取得できません。</div>
  </div>
</template>

<script>
import LogOut from '@/components/LogOut.vue'

export default {
  name: 'MyPage',
  components: { LogOut },
  data() {//data初期化
    return {
      user: null,
      form: {
        name: '',
        phone: '',
        address: '',
        postal_code: ''
      },
      editMode: false,
      editEmailMode: false,
      emailChangeRequested: false,
      newEmail: '',
      verificationCode: '',
      emailMessage: '',
      message: '',
      currentPassword: '',
      newPassword: '',
      confirmPassword: '',
      passwordMessage: '',
      showPasswordForm: false
    }
  },
  created() {
    const storedUser = localStorage.getItem('user')//ユーザーデータ
    if (storedUser) {
      this.user = JSON.parse(storedUser)
    }
  },
  methods: {
    //編集モード
    startEdit() {
      this.form = { ...this.user }
      this.editMode = true
    },
    //キャンセル
    cancelEdit() {
      this.editMode = false
      this.message = ''
    },
    //個人情報更新
    async handleUpdate() {
      try {
        const res = await fetch('/api/mypage/profile/change', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
          body: JSON.stringify(this.form),
        })
        const data = await res.json()
        if (res.ok) {
          this.message = '情報を更新しました。'
          this.user = { ...this.form }
          localStorage.setItem('user', JSON.stringify(this.user))//情報更新
          this.editMode = false
        } else {
          this.message = data.error || '更新に失敗しました。'
        }
      } catch (e) {
        this.message = '通信エラーが発生しました。'
      }
    },
    //メール更新
    async requestEmailChange() {
      try {
        const res = await fetch('/api/mypage/email/change', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
          body: JSON.stringify({ new_email: this.newEmail }),
        })
        const data = await res.json()
        if (res.ok) {
          this.emailMessage = '認証コードを送信しました。メールをご確認ください。'
          this.emailChangeRequested = true
        } else {
          this.emailMessage = data.error || '送信に失敗しました。'
        }
      } catch {
        this.emailMessage = '通信エラーが発生しました。'
      }
    },
    // cancelEmailEdit() {
    //   this.editEmailMode = false
    //   this.emailChangeRequested = false
    //   this.newEmail = ''
    //   this.verificationCode = ''
    //   this.emailMessage = ''
    // },
    // //メール認証コード確認
    // async verifyEmailChange() {
    //   try {
    //     const res = await fetch('/api/mypage/email/verify', {
    //       method: 'POST',
    //       headers: {
    //         'Content-Type': 'application/json',
    //         Authorization: 'Bearer ' + localStorage.getItem('token'),
    //       },
    //       body: JSON.stringify({
    //         new_email: this.newEmail,
    //         code: this.verificationCode
    //       }),
    //     })
    //     const data = await res.json()
    //     if (res.ok) {
    //       this.emailMessage = 'メールアドレスを変更しました。'
    //       this.user.email = this.newEmail
    //       localStorage.setItem('user', JSON.stringify(this.user))
    //       this.emailChangeRequested = false
    //       this.editEmailMode = false
    //       this.newEmail = ''
    //       this.verificationCode = ''
    //     } else {
    //       this.emailMessage = data.error || '認証に失敗しました。'
    //     }
    //   } catch {
    //     this.emailMessage = '通信エラーが発生しました。'
    //   }
    // },
    //パスワード更新
    async changePassword() {
      if (this.newPassword !== this.confirmPassword) {
        this.passwordMessage = '新しいパスワードが一致しません。'
        return
      }
      try {
        const res = await fetch('/api/mypage/password/change', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
          body: JSON.stringify({
            current_password: this.currentPassword,
            new_password: this.newPassword
          }),
        })
        const data = await res.json()
        if (res.ok) {
          this.passwordMessage = 'パスワードを変更しました。'
          this.currentPassword = ''
          this.newPassword = ''
          this.confirmPassword = ''
        } else {
          this.passwordMessage = data.error || '変更に失敗しました。'
        }
      } catch {
        this.passwordMessage = '通信エラーが発生しました。'
      }
    }
  }
}
</script>
