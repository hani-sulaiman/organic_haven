<template>
  <section class="product-tabs section-padding position-relative">
    <div class="container">
      <div class="section-title style-2 wow animate__animated animate__fadeIn">
        <h3>Popular Products</h3>
      </div>
      <!-- Product grid -->
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
          <div class="row product-grid-4">
            <div v-for="(product, index) in products" :key="product.id" class="col-lg-1-5 col-md-4 col-12 col-sm-6">
              <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn"
                :data-wow-delay="getDelay(index)">
                <div class="product-img-action-wrap">
                  <div class="product-img product-img-zoom">
                    <router-link :to="`/product/${product.id}`">
                      <img class="default-img" :src="product.img_url || '/assets/imgs/shop/default-product.jpg'"
                        alt="" />
                    </router-link>
                  </div>
                  <div class="product-badges product-badges-position product-badges-mrg" v-if="product.is_hot">
                    <span class="hot">Hot</span>
                  </div>
                </div>
                <div class="product-content-wrap">
                  <div class="product-category">
                    <router-link :to="`/category/${product.category_id}`">
                      {{ product.category_title || 'Category' }}
                    </router-link>
                  </div>
                  <h2>
                    <router-link :to="`/product/${product.id}`">
                      {{ product.title }}
                    </router-link>
                  </h2>
                  <div class="product-rate-cover">
                    <div class="product-rate d-inline-block">
                      <div class="product-rating" :style="{ width: product.rating * 20 + '%' }"></div>
                    </div>
                    <span class="font-small ml-5 text-muted"> ({{ product.rating }})</span>
                  </div>
                  <div>
                    <span class="font-small text-muted">
                      By <router-link :to="`/vendor/${product.vendor_id}`">
                        {{ product.vendor_name || 'Vendor' }}
                      </router-link>
                    </span>
                  </div>
                  <div class="product-card-bottom">
                    <div class="product-price">
                      <span>${{ parseFloat(product.price).toFixed(2) }}</span>
                      <span class="old-price" v-if="product.old_price">
                        ${{ parseFloat(product.old_price).toFixed(2) }}
                      </span>
                    </div>
                    <div class="add-cart">
                      <a class="add" href="#" @click.prevent="addToCart(product)">
                        <i class="fi-rs-shopping-cart mr-5"></i>Add
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End product card -->
            </div>
          </div>
          <!-- End product grid -->
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/api/index.js';
import emitter from '@/eventBus.js';
const products = ref([]);

const fetchPopularProducts = async () => {
  try {
    // Use the recommendation endpoint to fetch popular products
    const response = await apiClient.get('/recommend/general');
    if (response.data.success) {
      products.value = response.data.recommendations || [];
    }
  } catch (error) {
    console.error('Error fetching popular products:', error);
  }
};

onMounted(() => {
  fetchPopularProducts();
});

// Helper function for animation delay
const getDelay = (index) => {
  return `.${index + 1}s`;
};

// Add-to-cart action for product grid (default quantity = 1)
const addToCart = async (product) => {
  try {
    const response = await apiClient.post('/customer/cart', { product_id: product.id, quantity: 1 });
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

// Dummy add-to-wishlist function
const addToWishlist = (product) => {
  console.log('Add to wishlist:', product);
};
</script>

<style scoped>
/* Add any additional styles as needed */
</style>
