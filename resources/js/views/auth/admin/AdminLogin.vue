<template>
    <div class="admin-login">
      <h1>Admin Login</h1>
      <form @submit.prevent="login">
        <input type="email" v-model="email" placeholder="Email" required />
        <input type="password" v-model="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  
  export default {
    data() {
      return {
        email: '',
        password: '',
      };
    },
    methods: {
      async login() {
        try {
          const response = await apiClient.post('/login', { email: this.email, password: this.password });
          localStorage.setItem('token', response.data.access_token);
          localStorage.setItem('role', response.data.role);
          this.$router.push('/admin/dashboard');
        } catch (error) {
          console.error('Admin login failed:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .admin-login {
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
  }
  
  .admin-login form {
    display: flex;
    flex-direction: column;
  }
  
  .admin-login input {
    margin-bottom: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  .admin-login button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .admin-login button:hover {
    background-color: #0056b3;
  }
  </style>