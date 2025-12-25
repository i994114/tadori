<template>

        <ul class="l-sidebar__nav">
            <li class="l-sidebar__item">
                <router-link v-if="authUser" class="l-sidebar__item--accent" :to="{ name: 'step-create' }">STEP登録する</router-link>
            </li>
            <li class="l-sidebar__item">
                <router-link v-if="authUser" class="l-sidebar__item--link" :to="{ name: 'password-change', params: { id: authUser.id } }">パスワードを変更する</router-link>
            </li>
            <li class="l-sidebar__item">
                <router-link v-if="authUser" class="l-sidebar__item--link" :to="{ name: 'user-edit', params: { id: authUser.id } }">プロフィールを編集する</router-link>
            </li>
<!--
            <li class="l-sidebar__item">
                <router-link v-if="authUser" class="l-sidebar__item--link" :to="{ name: 'email-change', params: { id: authUser.id } }">メールアドレスを変更する</router-link>
            </li>
-->
        </ul>

</template>
<script>
import { mapGetters} from 'vuex'

export default {
    computed: {

        ...mapGetters('user', [
            'authUser'
        ]),

    },
}
</script>
