<template>
    <div class="min-h-screen flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center">Verify Your Email</h2>
        <form @submit.prevent="handleVerify">
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
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
              class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
              required
            />
          </div>
          <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded hover:bg-pink-700 transition duration-200">
            <span v-if="loading">Verifying...</span>
            <span v-else>Verify</span>
          </button>
          <div v-if="error" class="mt-4 text-red-600 text-center">{{ error }}</div>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
          Didn't receive the OTP?
          <button @click="resendOtp" class="text-pink-600 hover:underline">Resend OTP</button>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive, ref } from 'vue';
  import { useRouter, useRoute } from 'vue-router';
  import apiClient from '@/api/index.js';
  
  const router = useRouter();
  const route = useRoute();
  const loading = ref(false);
  const error = ref('');
  
  const form = reactive({
    email: route.query.email || '',
    otp_code: '',
  });
  
  const handleVerify = async () => {
    loading.value = true;
    error.value = '';
    try {
      const response = await apiClient.post('/verify-email', form);
      if (response.data.success) {
        router.push('/login');
      } else {
        error.value = response.data.message || 'Verification failed.';
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'An error occurred.';
    } finally {
      loading.value = false;
    }
  };
  
  const resendOtp = async () => {
    loading.value = true;
    error.value = '';
    try {
      const response = await apiClient.post('/resend-otp', { email: form.email });
      if (response.data.success) {
        alert('OTP has been resent to your email.');
      } else {
        error.value = response.data.message || 'Failed to resend OTP.';
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
  