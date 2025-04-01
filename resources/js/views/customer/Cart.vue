<template>
    <div class="container mb-80 mt-50">
      <div class="row">
        <!-- Cart Heading -->
        <div class="col-lg-8 mb-40">
          <h1 class="heading-2 mb-10">Your Cart</h1>
          <div class="d-flex justify-content-between">
            <h6 class="text-body">
              There are <span class="text-brand">{{ cart.length }}</span> products in your cart
            </h6>
            <h6 class="text-body">
              <a href="#" class="text-muted" @click.prevent="clearCart">
                <i class="fi-rs-trash mr-5"></i>Clear Cart
              </a>
            </h6>
          </div>
        </div>
      </div>
      <div class="row">
        <!-- Cart Items Table -->
        <div class="col-lg-8">
          <div class="table-responsive shopping-summery">
            <table class="table table-wishlist">
              <thead>
                <tr class="main-heading">
                  <th class="custome-checkbox start pl-30">
                    <!-- Checkbox disabled (for display only) -->
                    <input class="form-check-input" type="checkbox" disabled>
                  </th>
                  <th scope="col" colspan="2">Product</th>
                  <th scope="col">Unit Price</th>
                  <th scope="col">Quantity</th>
                  <th scope="col">Subtotal</th>
                  <th scope="col" class="end">Remove</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in cart" :key="item.id" class="pt-30">
                  <td class="custome-checkbox pl-30">
                    <input class="form-check-input" type="checkbox" disabled>
                  </td>
                  <td class="image product-thumbnail pt-40">
                    <img :src="item.product.img_url" alt="Product Image">
                  </td>
                  <td class="product-des product-name">
                    <h6 class="mb-5">
                      <router-link :to="`/product/${item.product.id}`" class="product-name mb-10 text-heading">
                        {{ item.product.title }}
                      </router-link>
                    </h6>
                    <div class="product-rate-cover">
                      <div class="product-rate d-inline-block">
                        <div class="product-rating" style="width:90%"></div>
                      </div>
                      <span class="font-small ml-5 text-muted"> ({{ item.product.rating || 0 }})</span>
                    </div>
                  </td>
                  <td class="price" data-title="Price">
                    <h4 class="text-body">${{ parseFloat(item.product.price).toFixed(2) }}</h4>
                  </td>
                  <td class="text-center detail-info" data-title="Quantity">
                    <div class="detail-extralink mr-15">
                      <div class="detail-qty border radius">
                        <a href="#" class="qty-down" @click.prevent="updateQuantity(item, item.quantity - 1)">
                          <i class="fi-rs-angle-small-down"></i>
                        </a>
                        <input type="text" name="quantity" class="qty-val" v-model.number="item.quantity" min="1">
                        <a href="#" class="qty-up" @click.prevent="updateQuantity(item, item.quantity + 1)">
                          <i class="fi-rs-angle-small-up"></i>
                        </a>
                      </div>
                    </div>
                  </td>
                  <td class="price" data-title="Subtotal">
                    <h4 class="text-brand">
                      ${{ (item.product.price * item.quantity).toFixed(2) }}
                    </h4>
                  </td>
                  <td class="action text-center" data-title="Remove">
                    <a href="#" class="text-body" @click.prevent="removeItem(item.id)">
                      <i class="fi-rs-trash"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="divider-2 mb-30"></div>
          <div class="cart-action d-flex justify-content-between">
            <router-link class="btn" to="/">
              <i class="fi-rs-arrow-left mr-10"></i>Continue Shopping
            </router-link>
            <!-- Optionally, an Update Cart button if needed -->
            <!-- <a class="btn mr-10 mb-sm-15" href="#">Update Cart</a> -->
          </div>
        </div>
        <!-- Cart Totals Sidebar -->
        <div class="col-lg-4">
          <div class="border p-md-4 cart-totals ml-30">
            <div class="table-responsive">
              <table class="table no-border">
                <tbody>
                  <tr>
                    <td class="cart_total_label">
                      <h6 class="text-muted">Total</h6>
                    </td>
                    <td class="cart_total_amount">
                      <h4 class="text-brand text-end">${{ total.toFixed(2) }}</h4>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <router-link to="/checkout" class="btn mb-20 w-100">
              Proceed To CheckOut<i class="fi-rs-sign-out ml-15"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import apiClient from '@/api/index.js';
  import emitter from '@/eventBus.js';
  const router = useRouter();
  const cart = ref([]);
  
  // Fetch the cart items from the backend
  const fetchCart = async () => {
    try {
      const response = await apiClient.get('/customer/cart');
      if (response.data.success) {

        cart.value = response.data.data;
      }
    } catch (error) {
      console.error('Error fetching cart:', error);
    }
  };
  
  // Update quantity for a cart item
  const updateQuantity = async (item, newQuantity) => {
    if (newQuantity < 1) return;
    try {
      const response = await apiClient.put(`/customer/cart/${item.id}`, { quantity: newQuantity });
      if (response.data.success) {
        emitter.emit('cartUpdated');
        await fetchCart();
      } else {
        alert('Failed to update cart item');
      }
    } catch (error) {
      console.error('Error updating cart item:', error);
      alert('Error updating cart item');
    }
  };
  
  // Remove a cart item
  const removeItem = async (itemId) => {
    try {
      const response = await apiClient.delete(`/customer/cart/${itemId}`);
      if (response.data.success) {
        emitter.emit('cartUpdated');
        await fetchCart();
      } else {
        alert('Failed to remove cart item');
      }
    } catch (error) {
      console.error('Error removing cart item:', error);
      alert('Error removing cart item');
    }
  };
  
  // Clear the entire cart
  const clearCart = async () => {
    try {
      const response = await apiClient.delete('/customer/cart');
      if (response.data.success) {
        emitter.emit('cartUpdated');
        await fetchCart();
      } else {
        alert('Failed to clear cart');
      }
    } catch (error) {
      console.error('Error clearing cart:', error);
      alert('Error clearing cart');
    }
  };
  
  // Computed total price of cart items
  const total = computed(() => {
    return cart.value.reduce((sum, item) => {
      return sum + parseFloat(item.product.price) * item.quantity;
    }, 0);
  });
  
  onMounted(() => {
    fetchCart();
  });
  </script>
  
  <style scoped>
  /* Add any custom styles for your cart page here */
  </style>
  