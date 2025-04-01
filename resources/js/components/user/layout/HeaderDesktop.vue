<template>
  <header class="header-area header-style-1 header-height-2">
    <!-- Top Middle Header (visible on large screens) -->
    <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
      <div class="container">
        <div class="header-wrap">
          <!-- Logo -->
          <div class="logo logo-width-1">
            <router-link to="/" class="logo-link">
              OrganicHaven
            </router-link>
          </div>
          <!-- Right Side: Search, Wishlist, Cart, Account -->
          <div class="header-right">
            <div class="search-style-2">
              <form @submit.prevent="handleSearch">
                <input
                  type="text"
                  v-model="searchQuery"
                  placeholder="Search for items..."
                />
              </form>
            </div>
            <div class="header-action-right">
              <div class="header-action-2">
                <!-- Wishlist -->
                <div class="header-action-icon-2">
                  <router-link to="/wishlist">
                    <img
                      class="svgInject"
                      alt="Wishlist"
                      src="/assets/user/imgs/theme/icons/icon-heart.svg"
                    />
                    <span class="pro-count blue">{{ wishlistCount }}</span>
                  </router-link>
                  <router-link to="/wishlist">
                    <span class="lable">Wishlist</span>
                  </router-link>
                </div>
                <!-- Cart -->
                <div class="header-action-icon-2">
                  <router-link class="mini-cart-icon" to="/cart">
                    <img
                      alt="Cart"
                      src="/assets/user/imgs/theme/icons/icon-cart.svg"
                    />
                    <span class="pro-count blue">{{ cartCount }}</span>
                  </router-link>
                  <router-link to="/cart">
                    <span class="lable">Cart</span>
                  </router-link>
                </div>
                <!-- Account -->
                <div class="header-action-icon-2">
                  <router-link v-if="isAuthenticated" to="/profile">
                    <img
                      class="svgInject"
                      alt="Account"
                      src="/assets/user/imgs/theme/icons/icon-user.svg"
                    />
                  </router-link>
                  <router-link v-else to="/login">
                    <img
                      class="svgInject"
                      alt="Account"
                      src="/assets/user/imgs/theme/icons/icon-user.svg"
                    />
                  </router-link>
                  <router-link v-if="isAuthenticated" to="/profile">
                    <span class="lable ml-0">{{ userName || 'Account' }}</span>
                  </router-link>
                  <router-link v-else to="/login">
                    <span class="lable ml-0">Login</span>
                  </router-link>
                </div>
                <!-- End Account -->
              </div>
            </div>
          </div>
          <!-- End header-right -->
        </div>
      </div>
    </div>
    <!-- Bottom Header with Navigation -->
    <div class="header-bottom header-bottom-bg-color sticky-bar">
      <div class="container">
        <div class="header-wrap header-space-between position-relative">
          <!-- Mobile Logo (visible on small screens) -->
          <div class="logo logo-width-1 d-block d-lg-none">
            <router-link to="/" class="logo-link">
              OrganicHaven
            </router-link>
          </div>
          <!-- Navigation menu -->
          <div class="header-nav d-none d-lg-flex">
            <!-- Categories dropdown -->
            <div class="main-categori-wrap d-none d-lg-block">
              <router-link class="categories-button-active" to="#">
                <span class="fi-rs-apps"></span>
                <span class="et">Browse</span> All Categories
                <i class="fi-rs-angle-down"></i>
              </router-link>
              <div class="categories-dropdown-wrap categories-dropdown-active-large font-heading">
                <div class="d-flex categori-dropdown-inner">
                  <ul>
                    <li v-for="category in categories" :key="category.id">
                      <router-link :to="`/products?categories=${category.id}`">
                        <img
                          :src="category.img_url || '/assets/user/imgs/theme/icons/default-category.svg'"
                          alt=""
                        />
                        {{ category.title }}
                      </router-link>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- Main menu -->
            <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-flex font-heading">
              <nav>
                <ul>
                  <li><router-link to="/">Home</router-link></li>
                  <li><router-link to="/admin">Admin</router-link></li>
                  <li><router-link to="/about">About</router-link></li>
                  <li><router-link to="/products">Shop</router-link></li>
                  <li><router-link to="/contact-us">Contact</router-link></li>
                  <li v-if="!isAuthenticated"><router-link to="/login">Login</router-link></li>
                </ul>
              </nav>
            </div>
          </div>
          <!-- Hotline -->
          <div class="hotline d-none d-lg-flex">
            <img src="/assets/user/imgs/theme/icons/icon-headphone.svg" alt="hotline" />
            <p>1900 - 888<span>24/7 Support Center</span></p>
          </div>
          <!-- Mobile menu icon (for small screens) -->
          <div class="header-action-icon-2 d-block d-lg-none">
            <div class="burger-icon burger-icon-white">
              <span class="burger-icon-top"></span>
              <span class="burger-icon-mid"></span>
              <span class="burger-icon-bottom"></span>
            </div>
          </div>
          <!-- Mobile wishlist icon -->
          <div class="header-action-right d-block d-lg-none">
            <div class="header-action-2">
              <div class="header-action-icon-2">
                <router-link to="/wishlist">
                  <img alt="Wishlist" src="/assets/user/imgs/theme/icons/icon-heart.svg" />
                  <span class="pro-count white">{{ wishlistCount }}</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import apiClient from '@/api/index.js';
import emitter from '@/eventBus.js'; // global event bus using Mitt

const router = useRouter();
const route = useRoute();

const wishlistCount = ref(0);
const cartCount = ref(0);
const categories = ref([]);
const searchQuery = ref('');
const showAccountDropdown = ref(false);
const userName = ref('');

const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const fetchWishlistCount = async () => {
  try {
    const response = await apiClient.get('/customer/wishlist');
    if (response.data.success) {
      wishlistCount.value = response.data.wishlist ? response.data.wishlist.length : 0;
    }
  } catch (error) {
    console.error('Error fetching wishlist:', error);
  }
};

const fetchCartCount = async () => {
  try {
    const response = await apiClient.get('/customer/cart');
    console.log('Cart response:', response.data);
    if (response.data.success) {
      cartCount.value = response.data.data ? response.data.data.length : 0;
    }
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await apiClient.get('/categories');
    if (response.data.success) {
      categories.value = response.data.data || response.data.categories || [];
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const fetchUserProfile = async () => {
  try {
    const response = await apiClient.get('/profile');
    if (response.data.success) {
      userName.value = response.data.user.name;
    }
  } catch (error) {
    console.error('Error fetching user profile (desktop):', error);
  }
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    if (route.path === '/products') {
      router.replace({ query: { q: searchQuery.value } });
    } else {
      router.push({ path: '/products', query: { q: searchQuery.value } });
    }
  }
};

const toggleAccountDropdown = () => {
  showAccountDropdown.value = !showAccountDropdown.value;
};

const logout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('role');
  router.push('/login');
};

const updateCartCount = () => {
  fetchCartCount();
};

const updateWishlistCount = () => {
  fetchWishlistCount();
};

emitter.on('cartUpdated', updateCartCount);
emitter.on('wishlistUpdated', updateWishlistCount);

onMounted(() => {
  fetchCategories();
  if (isAuthenticated.value) {
    fetchWishlistCount();
    fetchCartCount();
    fetchUserProfile();
  }
});

onBeforeUnmount(() => {
  emitter.off('cartUpdated', updateCartCount);
  emitter.off('wishlistUpdated', updateWishlistCount);
});
</script>

<style scoped>
.logo-link {
  font-size: 25px;
  font-weight: 900;
  color: #3bb77e;
  text-decoration: none;
}

/* Additional desktop header styles as needed */
</style>
