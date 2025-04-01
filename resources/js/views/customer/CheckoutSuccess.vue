<template>
    <div class="checkout-success">
      <div class="success-container">
        <div class="success-icon">
          <!-- SVG Check Mark Icon -->
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#4BB543" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
        </div>
        <h1>Payment Successful!</h1>
        <p>Thank you for your purchase. Your order has been placed successfully.</p>
        <router-link to="/" class="btn btn-success">Return to Home</router-link>
      </div>
    </div>
  </template>
  
  <script setup>
    // Additional logic can be added here if needed.
  </script>
  
  <style scoped>
  .checkout-success {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e0f9e9, #a8e6cf);
    padding: 20px;
  }
  .success-container {
    background: #fff;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  .success-icon {
    margin-bottom: 20px;
  }
  .success-icon svg {
    display: block;
    margin: 0 auto;
  }
  h1 {
    font-size: 2em;
    margin-bottom: 10px;
    color: #4BB543;
  }
  p {
    font-size: 1.1em;
    margin-bottom: 20px;
    color: #666;
  }
  .btn-success {
    background-color: #4BB543;
    color: #fff;
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }
  .btn-success:hover {
    background-color: #3a8e3f;
  }
  </style>
  