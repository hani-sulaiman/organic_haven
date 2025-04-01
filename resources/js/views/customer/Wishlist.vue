<template>
    <div class="container mb-30 mt-50">
      <div class="row">
        <div class="col-xl-10 col-lg-12 m-auto">
          <div class="mb-50">
            <h1 class="heading-2 mb-10">Your Wishlist</h1>
            <h6 class="text-body">
              There are <span class="text-brand">{{ wishlist.length }}</span> products in your list
            </h6>
          </div>
          <div v-if="wishlist.length" class="table-responsive shopping-summery">
            <table class="table table-wishlist">
              <thead>
                <tr class="main-heading">
                  <th class="custome-checkbox start pl-30">
                    <input class="form-check-input" type="checkbox" disabled />
                    <label class="form-check-label"></label>
                  </th>
                  <th scope="col" colspan="2">Product</th>
                  <th scope="col">Price</th>
                  <th scope="col">Action</th>
                  <th scope="col" class="end">Remove</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in wishlist" :key="item.id" class="pt-30">
                  <td class="custome-checkbox pl-30">
                    <input class="form-check-input" type="checkbox" disabled />
                    <label class="form-check-label"></label>
                  </td>
                  <td class="image product-thumbnail pt-40">
                    <img :src="item.image_url" alt="Product Image" />
                  </td>
                  <td class="product-des product-name">
                    <h6 class="mb-5">
                      <router-link :to="`/product/${item.id}`" class="product-name mb-10 text-heading">
                        {{ item.name }}
                      </router-link>
                    </h6>
                  </td>
                  <td class="price" data-title="Price">
                    <h3 class="text-brand">${{ parseFloat(item.price).toFixed(2) }}</h3>
                  </td>
                  <td class="text-right" data-title="Cart">
                    <button class="btn btn-sm" @click="addToCart(item)">Add to cart</button>
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
          <div v-else class="alert alert-warning">
            Your wishlist is empty.
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import apiClient from '@/api/index.js';
  import emitter from '@/eventBus.js';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  const wishlist = ref([]);
  
  const fetchWishlist = async () => {
    try {
      const response = await apiClient.get('/customer/wishlist');
      if (response.data.success) {
        // The API is expected to return the wishlist in a "wishlist" key.
        wishlist.value = response.data.wishlist || [];
      }
    } catch (error) {
      console.error('Error fetching wishlist:', error);
    }
  };
  
  const removeItem = async (productId) => {
    try {
      const response = await apiClient.delete(`/customer/wishlist/remove/${productId}`);
      if (response.data.success) {
        await fetchWishlist();
        emitter.emit('wishlistUpdated');
        alert('Product removed from wishlist successfully');
      } else {
        alert('Failed to remove product from wishlist');
      }
    } catch (error) {
      console.error('Error removing wishlist item:', error);
      alert('Error removing wishlist item');
    }
  };
  
  const addToCart = async (item) => {
    try {
      const response = await apiClient.post('/customer/cart', { product_id: item.id, quantity: 1 });
      if (response.data.success) {
        alert('Product added to cart successfully');
        emitter.emit('cartUpdated');
      } else {
        alert('Failed to add product to cart');
      }
    } catch (error) {
      console.error('Error adding product to cart:', error);
      alert('Error adding product to cart');
    }
  };
  
  onMounted(() => {
    fetchWishlist();
  });
  </script>
  
  <style scoped>
  /* Basic styling for wishlist page */
  .alert {
    padding: 15px;
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
  }
  </style>
  