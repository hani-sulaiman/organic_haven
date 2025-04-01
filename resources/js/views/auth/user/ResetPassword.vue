<template>
    <div class="min-h-screen flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
        <form @submit.prevent="handleResetPassword">
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
              readonly
            />
          </div>
          <div class="mb-4">
            <label for="otp_code" class="block text-gray-700">OTP Code</label>
            <input
              v-model="form.otp_code"
              type="text"
              id="otp_code"
              maxlength="6"
              placeholder="Enter OTP"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
              required
            />
          </div>
          <div class="mb-4">
            <label for="password" class="block text-gray-700">New Password</label>
            <input
              v-model="form.password"
              type="password"
              id="password"
              placeholder="New password"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
              required
            />
          </div>
          <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input
              v-model="form.password_confirmation"
              type="password"
              id="password_confirmation"
              placeholder="Confirm new password"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
              required
            />
          </div>
          <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition duration-200">
            <span v-if="loading">Resetting Password...</span>
            <span v-else>Reset Password</span>
          </button>
          <div v-if="error" class="mt-4 text-red-600 text-center">{{ error }}</div>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
          Remembered your password?
          <router-link to="/login" class="text-green-600 hover:underline">Login</router-link>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import apiClient from '@/api/index.js';
  
  const router = useRouter();
  const route = useRoute();
  const loading = ref(false);
  const error = ref('');
  
  const form = reactive({
    email: route.query.email || '',
    otp_code: '',
    password: '',
    password_confirmation: '',
  });
  
  const handleResetPassword = async () => {
    loading.value = true;
    error.value = '';
    try {
      const response = await apiClient.post('/password-reset/reset', form);
      if (response.data.success) {
        router.push('/login');
      } else {
        error.value = response.data.message || 'Reset password failed.';
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'An error occurred.';
    } finally {
      loading.value = false;
    }
  };
  </script>
  
  <style scoped>
  /* Additional creative styles */
  </style>
  