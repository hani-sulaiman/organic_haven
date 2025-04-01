<template>
    <div class="admin-layout">
      <aside class="navbar-aside" id="offcanvas_aside">
        <div class="aside-top">
          <a href="#" class="brand-wrap">
            <h2>Admin Panel</h2>
          </a>
          <div>
            <button class="btn btn-icon btn-aside-minimize" @click="toggleAside">
              <i class="text-muted material-icons md-menu_open"></i>
            </button>
          </div>
        </div>
        <nav>
          <ul class="menu-aside">
            <li class="menu-item" :class="{ active: $route.name === 'admin.dashboard' || $route.path === '/admin'  }">
              <router-link class="menu-link" :to="{ name: 'admin.dashboard' }">
                <i class="icon material-icons md-home"></i>
                <span class="text">Dashboard</span>
              </router-link>
            </li>
            <li class="menu-item" :class="{ active: $route.name === 'admin.products.index' }">
              <router-link class="menu-link" :to="{ name: 'admin.products.index' }">
                <i class="icon material-icons md-shopping_bag"></i>
                <span class="text">Products</span>
              </router-link>
            </li>
            <li class="menu-item" :class="{ active: $route.name === 'admin.categories.index' }">
              <router-link class="menu-link" :to="{ name: 'admin.categories.index' }">
                <i class="icon material-icons md-menu"></i>
                <span class="text">Categories</span>
              </router-link>
            </li>
            <li class="menu-item" :class="{ active: $route.name === 'admin.orders.index' }">
              <router-link class="menu-link" :to="{ name: 'admin.orders.index' }">
                <i class="icon material-icons md-shopping_cart"></i>
                <span class="text">Orders</span>
              </router-link>
            </li>
            <li class="menu-item" :class="{ active: $route.name === 'admin.ai-model' }">
              <router-link class="menu-link" :to="{ name: 'admin.ai-model' }">
                <i class="icon fa fa-asterisk"></i>
                <span class="text">AI Model</span>
              </router-link>
            </li>
          </ul>
          <hr />
          <ul class="menu-aside">
            <li class="menu-item">
              <button class="menu-link" style="border: none;" @click="logout">
                <i class="icon fas fa-sign-out-alt"></i>
                <span class="text">Logout</span>
              </button>
            </li>
          </ul>
        </nav>
      </aside>
      <main class="main-wrap">
        <router-view></router-view>
      </main>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  
  export default {
    methods: {
      toggleAside() {
        document.getElementById('offcanvas_aside').classList.toggle('minimized');
      },
      async logout() {
        try {
          await apiClient.post('/logout');
          localStorage.removeItem('token');
          localStorage.removeItem('role');
          window.location.replace('/admin/login')
        } catch (error) {
          console.error('Error logging out:', error);
        }
      },
    },
  };
  </script>