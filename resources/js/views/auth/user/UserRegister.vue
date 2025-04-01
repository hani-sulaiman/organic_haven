<template>
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-6 text-center">Sign Up</h2>
            <form @submit.prevent="handleRegister">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input v-model="form.name" type="text" id="name" placeholder="Your full name"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input v-model="form.email" type="email" id="email" placeholder="you@example.com"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                <div class="mb-4">
                    <label for="birthdate" class="block text-gray-700">Birthdate</label>
                    <input v-model="form.birthdate" type="date" id="birthdate"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="mb-4">
                    <label for="gender" class="block text-gray-700">Gender</label>
                    <select v-model="form.gender" id="gender"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input v-model="form.password" type="password" id="password" placeholder="Create a password"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
                    <input v-model="form.password_confirmation" type="password" id="password_confirmation"
                        placeholder="Confirm your password"
                        class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-200">
                    <span v-if="loading">Signing up...</span>
                    <span v-else>Sign Up</span>
                </button>
                <div v-if="error" class="mt-4 text-red-600 text-center">{{ error }}</div>
            </form>
            <p class="mt-6 text-center text-sm text-gray-600">
                Already have an account?
                <router-link to="/login" class="text-blue-600 hover:underline">Login</router-link>
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
    name: '',
    email: '',
    birthdate: '',
    gender: '',
    password: '',
    password_confirmation: '',
});

const handleRegister = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await apiClient.post('/register', form);
        if (response.data.success) {
            // After registration, redirect to the email verification page
            router.push({ name: 'verify-email', query: { email: form.email } });
        } else {
            error.value = response.data.message || 'Registration failed.';
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'An error occurred.';
    } finally {
        loading.value = false;
    }
};
</script>
