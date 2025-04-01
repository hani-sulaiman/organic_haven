import { createRouter, createWebHistory } from 'vue-router';
import { loadScripts } from '@/utils/loadScripts';
import AdminLayout from '../layouts/AdminLayout.vue';
import UserLayout from '../layouts/UserLayout.vue';
import AdminLogin from '../views/auth/admin/AdminLogin.vue';
import UserLogin from '../views/auth/user/UserLogin.vue';
import Dashboard from '../views/admin/Dashboard.vue';
import ProductsList from '../views/admin/products/ProductsList.vue';
import CategoriesList from '../views/admin/categories/CategoriesList.vue';
import OrdersList from '../views/admin/orders/OrdersList.vue';
import OrderShow from '../views/admin/orders/OrderShow.vue';
import AiModel from '../views/admin/AiModel.vue';
import UserRegister from '../views/auth/user/UserRegister.vue';
import VerifyEmail from '@/views/auth/user/VerifyEmail.vue';
import ForgotPassword from '@/views/auth/user/ForgotPassword.vue';
import ResetPassword from '@/views/auth/user/ResetPassword.vue';

// Customer views
import Home from '../views/customer/Home.vue';
import About from '../views/customer/About.vue';
import Checkout from '../views/customer/Checkout.vue';
import ContactUs from '../views/customer/ContactUs.vue';
import ProductDetails from '../views/customer/ProductDetails.vue';
import Profile from '../views/customer/Profile.vue';
import Wishlist from '../views/customer/Wishlist.vue';
import Filter from '../views/customer/Filter.vue';
import Cart from '../views/customer/Cart.vue';
import CheckoutSuccess from '../views/customer/CheckoutSuccess.vue';
import CheckoutFailure from '../views/customer/CheckoutFailure.vue';

const routes = [
  {
    path: '/',
    component: UserLayout,
    children: [
      {
        path: '',
        component: Home,
        name: 'home',
      },
      {
        path: 'login',
        component: UserLogin,
        name: 'user.login',
      },
      {
        path: 'register',
        component: UserRegister,
        name: 'user.register',
      },
      { path: 'verify-email', name: 'verify-email', component: VerifyEmail },
      { path: 'forgot-password', name: 'forgot-password', component: ForgotPassword },
      { path: 'reset-password', name: 'reset-password', component: ResetPassword },
      {
        path: 'about',
        component: About,
        name: 'about',
      },
      {
        path: 'cart',
        component: Cart,
        name: 'cart',
        meta: { requiresAuth: true, role: 'customer' },
      },
      {
        path: 'checkout',
        component: Checkout,
        name: 'checkout',
        meta: { requiresAuth: true, role: 'customer' },
      },
      {
        path: 'checkout-success',
        component: CheckoutSuccess,
        name: 'checkout-success',
        meta: { requiresAuth: true, role: 'customer' },
      },
      {
        path: 'checkout-failure',
        component: CheckoutFailure,
        name: 'checkout-failure',
        meta: { requiresAuth: true, role: 'customer' },
      },
      {
        path: 'contact-us',
        component: ContactUs,
        name: 'contact-us',
      },
      {
        path: 'products',
        component: Filter,
        name: 'products.list',
      },
      {
        path: 'product/:id',
        component: ProductDetails,
        name: 'product.details',
      },
      {
        path: 'profile',
        component: Profile,
        name: 'profile.user',
        meta: { requiresAuth: true, role: 'customer' },
      },
      {
        path: 'wishlist',
        component: Wishlist,
        name: 'wishlist.user',
        meta: { requiresAuth: true, role: 'customer' },
      },
      // Add other user routes here
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    name: 'admin.layout',
    children: [
      {
        path: 'login',
        component: AdminLogin,
        name: 'admin.login',
        meta: { requiresAuth: false },
      },
      {
        path: 'dashboard',
        component: Dashboard,
        name: 'admin.dashboard',
        meta: { requiresAuth: true, role: 'admin' },
      },
      {
        path: 'products',
        component: ProductsList,
        name: 'admin.products.index',
        meta: { requiresAuth: true, role: 'admin' },
      },
      {
        path: 'categories',
        component: CategoriesList,
        name: 'admin.categories.index',
        meta: { requiresAuth: true, role: 'admin' },
      },
      {
        path: 'orders',
        component: OrdersList,
        name: 'admin.orders.index',
        meta: { requiresAuth: true, role: 'admin' },
      },
      {
        path: 'orders/:id',
        component: OrderShow,
        name: 'admin.orders.show',
        meta: { requiresAuth: true, role: 'admin' },
      },
      {
        path: 'ai-model',
        component: AiModel,
        name: 'admin.ai-model',
        meta: { requiresAuth: true, role: 'admin' },
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// List of scripts to load for customer side
const customerScriptsToLoad = [
  '/assets/user/js/vendor/modernizr-3.6.0.min.js',
  '/assets/user/js/vendor/jquery-3.6.0.min.js',
  '/assets/user/js/vendor/jquery-migrate-3.3.0.min.js',
  '/assets/user/js/vendor/bootstrap.bundle.min.js',
  '/assets/user/js/plugins/slick.js',
  '/assets/user/js/plugins/jquery.syotimer.min.js',
  '/assets/user/js/plugins/waypoints.js',
  '/assets/user/js/plugins/wow.js',
  '/assets/user/js/plugins/perfect-scrollbar.js',
  '/assets/user/js/plugins/magnific-popup.js',
  '/assets/user/js/plugins/select2.min.js',
  '/assets/user/js/plugins/counterup.js',
  '/assets/user/js/plugins/jquery.countdown.min.js',
  '/assets/user/js/plugins/images-loaded.js',
  '/assets/user/js/plugins/isotope.js',
  '/assets/user/js/plugins/scrollup.js',
  '/assets/user/js/plugins/jquery.vticker-min.js',
  '/assets/user/js/plugins/jquery.theia.sticky.js',
  '/assets/user/js/plugins/jquery.elevatezoom.js',
  '/assets/user/js/main.js?v=6.0',
  '/assets/user/js/shop.js?v=6.0',
];

// List of scripts to load for admin side
const adminScriptsToLoad = [
  '/assets/admin/js/vendors/jquery-3.6.0.min.js',
  '/assets/admin/js/vendors/bootstrap.bundle.min.js',
  '/assets/admin/js/vendors/select2.min.js',
  '/assets/admin/js/vendors/perfect-scrollbar.js',
  '/assets/admin/js/vendors/jquery.fullscreen.min.js',
  '/assets/admin/js/vendors/chart.js',
  '/assets/admin/js/main.js?v=6.0',
  '/assets/admin/js/custom-chart.js',
];

router.afterEach(async (to) => {
  try {
    const isCustomerRoute = !to.path.startsWith('/admin');
    const scriptsToLoad = isCustomerRoute ? customerScriptsToLoad : adminScriptsToLoad;
    await loadScripts(scriptsToLoad);
  } catch (error) {
    console.error('Failed to load scripts:', error);
  }
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const role = localStorage.getItem('role');

  // Allow access to admin login without redirection
  if (to.path === '/admin/login') {
    // Optionally, if an admin is already logged in, redirect them to the dashboard:
    if (token && role === 'admin') {
      return next({ path: '/admin/dashboard' });
    }
    return next();
  }

  // For all other admin routes
  if (to.path.startsWith('/admin')) {
    if (!token || role !== 'admin') {
      return next({ path: '/admin/login' });
    }
  } 
  // For routes requiring authentication on the customer side
  else if (to.meta.requiresAuth) {
    if (!token) {
      return next({ path: '/login' });
    }
    // If a specific role is required for the route and it doesn't match
    if (to.meta.role && to.meta.role !== role) {
      return next({ path: '/' });
    }
  }

  next();
});

export default router;
