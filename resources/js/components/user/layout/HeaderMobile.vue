<template>
  <div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
      <div class="mobile-header-top">
        <div class="mobile-header-logo">
          <router-link to="/" class="mobile-logo-link">
            OrganicHaven
          </router-link>
        </div>
        <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
          <button class="close-style search-close" @click="$emit('closeMobileMenu')">
            <i class="icon-top"></i>
            <i class="icon-bottom"></i>
          </button>
        </div>
      </div>
      <div class="mobile-header-content-area">
        <div class="mobile-search search-style-3 mobile-header-border">
          <form @submit.prevent="handleSearch">
            <input type="text" v-model="searchQuery" placeholder="Search for items…" />
            <button type="submit">
              <i class="fi-rs-search"></i>
            </button>
          </form>
        </div>
        <!-- Mobile Menu -->
        <div class="mobile-menu-wrap mobile-header-border">
          <nav>
            <ul class="mobile-menu font-heading">
              <li class="menu-item-has-children">
                <router-link to="/">Home</router-link>
              </li>
              <li class="menu-item-has-children">
                <router-link to="/products">Shop</router-link>
              </li>
              <li class="menu-item-has-children" v-if="!isAuthenticated">
                <router-link to="/login">Login / Sign Up</router-link>
              </li>
              <li class="menu-item-has-children" v-else>
                <router-link to="/profile">My Account</router-link>
              </li>
            </ul>
          </nav>
        </div>
        <!-- Mobile Header Info -->
        <div class="mobile-header-info-wrap">
          <div class="single-mobile-header-info">
            <router-link to="/contact-us">
              <i class="fi-rs-marker"></i> Our location
            </router-link>
          </div>
          <div class="single-mobile-header-info">
            <router-link v-if="!isAuthenticated" to="/login">
              <i class="fi-rs-user"></i>Login / Sign Up
            </router-link>
            <router-link v-else to="/profile">
              <i class="fi-rs-user"></i> {{ userName || 'My Account' }}
            </router-link>
          </div>
          <div class="single-mobile-header-info">
            <a href="#"><i class="fi-rs-headphones"></i>(+01) - 2345 - 6789</a>
          </div>
        </div>
        <!-- Mobile Social Icons -->
        <div class="mobile-social-icon mb-50">
          <h6 class="mb-15">Follow Us</h6>
          <a href="#"><img src="/assets/user/imgs/theme/icons/icon-facebook-white.svg" alt="" /></a>
          <a href="#"><img src="/assets/user/imgs/theme/icons/icon-twitter-white.svg" alt="" /></a>
          <a href="#"><img src="/assets/user/imgs/theme/icons/icon-instagram-white.svg" alt="" /></a>
          <a href="#"><img src="/assets/user/imgs/theme/icons/icon-pinterest-white.svg" alt="" /></a>
          <a href="#"><img src="/assets/user/imgs/theme/icons/icon-youtube-white.svg" alt="" /></a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import apiClient from '@/api/index.js';

const router = useRouter();
const route = useRoute();

const searchQuery = ref('');
const userName = ref('');
const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/products', query: { q: searchQuery.value } });
  }
};

const fetchUserProfile = async () => {
  try {
    const response = await apiClient.get('/profile');
    if (response.data.success) {
      userName.value = response.data.user.name;
    }
  } catch (error) {
    console.error('Error fetching user profile (mobile):', error);
  }
};

onMounted(() => {
  if (isAuthenticated.value) {
    fetchUserProfile();
  }
});
</script>


<style scoped>
.mobile-logo-link {
  font-size: 15px;
  font-weight: 900;
  color: #3bb77e;
  text-decoration: none;
}
/* Add any mobile-specific header styles if needed */
</style>
