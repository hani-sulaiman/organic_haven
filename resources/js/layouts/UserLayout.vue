<template>
    <HeaderDesktop />
    <HeaderMobile />
    <!--End header-->
    <main class="main pages">
        <router-view></router-view>
    </main>
    <Footer />

</template>
<script>
import HeaderDesktop from '@/components/user/layout/HeaderDesktop.vue'
import HeaderMobile from '@/components/user/layout/HeaderMobile.vue'
import Footer from '@/components/user/layout/Footer.vue'
export default {
    components: {
        HeaderDesktop, HeaderMobile, Footer
    }
}
</script>