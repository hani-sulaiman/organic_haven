<template>
  <div class="min-h-screen flex items-center justify-center ">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
      <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label for="email" class="block text-gray-700">Email</label>
          <input
            v-model="form.email"
            type="email"
            id="email"
            placeholder="you@example.com"
            class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            required
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-gray-700">Password</label>
          <input
            v-model="form.password"
            type="password"
            id="password"
            placeholder="Your password"
            class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            required
          />
        </div>
        <div class="mb-4 text-right">
          <router-link to="/forgot-password" class="text-sm text-indigo-600 hover:underline">
            Forgot Password?
          </router-link>
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition duration-200">
          <span v-if="loading">Logging in...</span>
          <span v-else>Login</span>
        </button>
        <div v-if="error" class="mt-4 text-red-600 text-center">{{ error }}</div>
      </form>
      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <router-link to="/register" class="text-indigo-600 hover:underline">Sign up</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '@/api/index.js';

const router = useRouter();
const loading = ref(false);
const error = ref('');

const form = reactive({
  email: '',
  password: '',
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await apiClient.post('/login', form);
    if (response.data.success) {
      // Store token and role in localStorage
      localStorage.setItem('token', response.data.access_token);
      localStorage.setItem('role', response.data.role);
      router.push('/');
    } else {
      error.value = response.data.message || 'Login failed.';
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'An error occurred.';
  } finally {
    loading.value = false;
  }
};
</script>
