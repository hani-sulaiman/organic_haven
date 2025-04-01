<template>
    <Slider />
    <Categories />
    <Products />
</template>

<script>
import Slider from '../../components/inc/Slider.vue';
import Categories from '../../components/home/Categories.vue';
import Products from '../../components/home/Products.vue';
export default {
    components: {
        Slider,
        Categories,
        Products
    }
}
</script>

