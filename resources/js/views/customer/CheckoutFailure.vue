<template>
    <div class="checkout-failure">
      <div class="failure-container">
        <div class="failure-icon">
          <!-- SVG Cross Icon -->
          <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#FF4F4F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </div>
        <h1>Payment Failed</h1>
        <p>We're sorry, your payment could not be processed. Please try again.</p>
        <router-link to="/checkout" class="btn btn-failure">Try Again</router-link>
      </div>
    </div>
  </template>
  
<script setup>
    // Additional logic can be added here if needed.
  </script>
  
  <style scoped>
  .checkout-failure {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #fbeaea, #f4a9a9);
    padding: 20px;
  }
  .failure-container {
    background: #fff;
    border-radius: 10px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }
  .failure-icon {
    margin-bottom: 20px;
  }
  .failure-icon svg {
    display: block;
    margin: 0 auto;
  }
  h1 {
    font-size: 2em;
    margin-bottom: 10px;
    color: #FF4F4F;
  }
  p {
    font-size: 1.1em;
    margin-bottom: 20px;
    color: #666;
  }
  .btn-failure {
    background-color: #FF4F4F;
    color: #fff;
    padding: 12px 30px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }
  .btn-failure:hover {
    background-color: #e04343;
  }
  </style>
  