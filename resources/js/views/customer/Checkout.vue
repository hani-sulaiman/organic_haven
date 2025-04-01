<template>
  <div class="checkout">
    <h2>Checkout</h2>
    <!-- Order Summary Section -->
    <div v-if="orderDetails" class="order-summary">
      <h3>Order Summary</h3>
      <ul>
        <li v-for="item in orderDetails.order_items" :key="item.id">
          {{ item.product.title }} - Qty: {{ item.quantity }} - Subtotal: ${{ (item.price * item.quantity).toFixed(2) }}
        </li>
      </ul>
      <h4>Total: ${{ parseFloat(orderDetails.total).toFixed(2) }}</h4>
    </div>
    <div v-else class="alert alert-warning">
      {{ errorMessage }}
    </div>

    <!-- Payment Form -->
    <form id="payment-form" v-if="orderDetails" @submit.prevent="handleSubmit">
      <div id="payment-element"></div>
      <button id="submit" :disabled="loading">
        <span v-if="!loading">Pay Now</span>
        <span v-else>Processing...</span>
      </button>
      <div id="error-message" v-if="errorMessage && orderDetails">{{ errorMessage }}</div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { loadStripe } from '@stripe/stripe-js';
import apiClient from '@/api/index.js';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref('');
const stripe = ref(null);
const elements = ref(null);
const clientSecret = ref('');
const orderDetails = ref(null);

const publishableKey = import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY;

// Fetch pending order details
const fetchOrderDetails = async () => {
  try {
    const response = await apiClient.get('/customer/pending-orders');
    if (response.data.success && response.data.data) {
      orderDetails.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching pending order:', error);
  }
};

// Fetch cart items to check if the cart is empty
const fetchCartItems = async () => {
  try {
    const response = await apiClient.get('/customer/cart');
    if (response.data.success && response.data.data && response.data.data.length > 0) {
      return response.data.data;
    }
    return [];
  } catch (error) {
    console.error('Error fetching cart items:', error);
    return [];
  }
};

// Convert cart to order (if no pending order exists)
const convertCartToOrder = async () => {
  try {
    const response = await apiClient.post('/customer/orders');
    if (response.data.success && response.data.data) {
      orderDetails.value = response.data.data;
      return true;
    } else {
      errorMessage.value = 'Error converting cart to order.';
      return false;
    }
  } catch (error) {
    console.error('Error converting cart to order:', error);
    errorMessage.value = 'Error converting cart to order.';
    return false;
  }
};

onMounted(async () => {
  // First, try to fetch an existing pending order.
  await fetchOrderDetails();
  
  // If no pending order exists, check the cart.
  if (!orderDetails.value) {
    const cartItems = await fetchCartItems();
    if (cartItems.length === 0) {
      errorMessage.value = 'Your cart is empty. Please add products.';
      return;
    }
    // Convert the non-empty cart to a pending order.
    const converted = await convertCartToOrder();
    if (!converted) return;
  }
  
  // Load Stripe
  stripe.value = await loadStripe(publishableKey);
  if (!stripe.value) {
    errorMessage.value = 'Failed to load Stripe.';
    return;
  }
  
  // Create a Payment Intent on the backend.
  try {
    const { data } = await apiClient.post('/customer/create-payment-intent');
    if (data.success) {
      clientSecret.value = data.client_secret;
    } else {
      errorMessage.value = 'Error creating payment intent.';
      return;
    }
  } catch (error) {
    console.error('Payment Intent error:', error);
    errorMessage.value = 'Error creating payment intent.';
    return;
  }
  
  // Initialize Stripe Elements with the client secret.
  elements.value = stripe.value.elements({ clientSecret: clientSecret.value });
  const paymentElement = elements.value.create('payment');
  paymentElement.mount('#payment-element');
});

const handleSubmit = async () => {
  loading.value = true;
  errorMessage.value = '';

  // Confirm the payment without automatic redirection.
  const result = await stripe.value.confirmPayment({
    elements: elements.value,
    // No return_url since redirection is handled manually
    confirmParams: {},
    redirect: 'if_required'
  });

  if (result.error) {
    // Update the order as failed.
    await apiClient.post('/customer/update-order-status', {
      order_id: orderDetails.value.id,
      status: 'failed'
    });
    router.push('/checkout-failure');
  } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
    // Update the order as paid.
    await apiClient.post('/customer/update-order-status', {
      order_id: orderDetails.value.id,
      status: 'paid'
    });
    router.push('/checkout-success');
  }

  loading.value = false;
};
</script>

  
  <style scoped>
  .checkout {
    max-width: 500px;
    margin: 40px auto;
    padding: 20px;
  }
  .order-summary {
    border: 1px solid #eaeaea;
    padding: 20px;
    margin-bottom: 20px;
  }
  #payment-element {
    margin-bottom: 20px;
  }
  #submit {
    background-color: #3bb77e;
    color: white;
    border: none;
    padding: 12px 20px;
    width: 100%;
    font-size: 16px;
    cursor: pointer;
  }
  #submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  #error-message {
    color: red;
    margin-top: 12px;
  }
  .alert {
    padding: 15px;
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
  }
  </style>
  