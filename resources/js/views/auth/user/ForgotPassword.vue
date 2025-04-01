<template>
    <div class="min-h-screen flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center">Forgot Password</h2>
        <form @submit.prevent="handleRequestOtp">
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              placeholder="you@example.com"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
              required
            />
          </div>
          <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition duration-200">
            <span v-if="loading">Sending OTP...</span>
            <span v-else>Send OTP</span>
          </button>
          <div v-if="error" class="mt-4 text-red-600 text-center">{{ error }}</div>
          <div v-if="success" class="mt-4 text-green-600 text-center">{{ success }}</div>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
          Remembered your password?
          <router-link to="/login" class="text-red-600 hover:underline">Login</router-link>
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
  const success = ref('');
  
  const form = reactive({
    email: '',
  });
  
  const handleRequestOtp = async () => {
    loading.value = true;
    error.value = '';
    success.value = '';
    try {
      const response = await apiClient.post('/password-reset/request', form);
      if (response.data.success) {
        success.value = 'OTP has been sent to your email.';
        // After a short delay, redirect to the Reset Password page
        setTimeout(() => {
          router.push({ name: 'reset-password', query: { email: form.email } });
        }, 2000);
      } else {
        error.value = response.data.message || 'Failed to send OTP.';
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
  