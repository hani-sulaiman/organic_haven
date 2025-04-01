<template>
    <div>
      <!-- Main Product Details -->
      <div v-if="product" class="container mb-30">
        <div class="row">
          <div class="col-xl-10 col-lg-12 m-auto">
            <div class="product-detail accordion-detail">
              <div class="row mb-50 mt-30">
                <!-- Gallery -->
                <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
                  <div class="detail-gallery">
                    <span class="zoom-icon">
                      <i class="fi-rs-search"></i>
                    </span>
                    <div class="product-image-slider">
                      <figure class="border-radius-10">
                        <img :src="product.img_url" alt="product image" />
                      </figure>
                    </div>
                  </div>
                </div>
                <!-- Product Info -->
                <div class="col-md-6 col-sm-12 col-xs-12">
                  <div class="detail-info pr-30 pl-30">
                    <h2 class="title-detail">{{ product.title }}</h2>
                    <div class="product-detail-rating">
                      <div class="product-rate-cover text-end">
                        <div class="product-rate d-inline-block">
                          <div class="product-rating" :style="{ width: ratingWidth }"></div>
                        </div>
                        <span class="font-small ml-5 text-muted"> ({{ reviewCount }} reviews)</span>
                      </div>
                    </div>
                    <div class="clearfix product-price-cover">
                      <div class="product-price primary-color float-left">
                        <span class="current-price text-brand">
                          ${{ parseFloat(product.price).toFixed(2) }}
                        </span>
                      </div>
                    </div>
                    <div class="short-desc mb-30">
                      <p class="font-lg">{{ product.ingredients }}</p>
                    </div>
                    <div class="attr-detail attr-size mb-30">
                      <strong class="mr-10">Size / Weight: </strong>
                      <ul class="list-filter size-filter font-small">
                        <li class="active">
                          <a href="#">{{ product.unit_value }} {{ product.unit_type }}</a>
                        </li>
                      </ul>
                    </div>
                    <div class="detail-extralink mb-50">
                      <div class="detail-qty border radius">
                        <a href="#" class="qty-down" @click.prevent="decrementQuantity">
                          <i class="fi-rs-angle-small-down"></i>
                        </a>
                        <input type="text" name="quantity" class="qty-val" v-model.number="quantity" min="1" />
                        <a href="#" class="qty-up" @click.prevent="incrementQuantity">
                          <i class="fi-rs-angle-small-up"></i>
                        </a>
                      </div>
                      <div class="product-extra-link2">
                        <button type="button" class="button button-add-to-cart" @click="addToCart(product)">
                          <i class="fi-rs-shopping-cart"></i>Add to cart
                        </button>
                        <!-- Wishlist Toggle Button -->
                        <a
                          aria-label="Toggle Wishlist"
                          class="action-btn hover-up"
                          href="#"
                          @click.prevent="toggleWishlist(product)"
                        >
                          <i :class="{'fa-heart': true, 'fa-solid': product.in_wishlist, 'fa-regular': !product.in_wishlist}"></i>
                        </a>
                      </div>
                    </div>
                    <div class="font-xs">
                      <ul class="mr-50 float-start">
                        <li class="mb-5">Type: <span class="text-brand">Organic</span></li>
                        <li class="mb-5">MFG:<span class="text-brand"> Jun 4.2024</span></li>
                        <li>LIFE: <span class="text-brand">70 days</span></li>
                      </ul>
                      <ul class="float-start">
                        <li class="mb-5">SKU: <a href="#">FWM15VKT</a></li>
                        <li class="mb-5">
                          Tags:
                          <a href="#" rel="tag">Snack</a>,
                          <a href="#" rel="tag">Organic</a>,
                          <a href="#" rel="tag">Brown</a>
                        </li>
                        <li>
                          Stock:
                          <span class="in-stock text-brand ml-5">8 Items In Stock</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Product Description Tabs -->
              <div class="product-info">
                <div class="tab-style3">
                  <ul class="nav nav-tabs text-uppercase">
                    <li class="nav-item">
                      <a class="nav-link active" id="Description-tab" data-bs-toggle="tab" href="#Description">
                        Description
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content shop_info_tab entry-main-content">
                    <div class="tab-pane fade show active" id="Description">
                      <div>
                        <p>{{ product.ingredients }}</p>
                        <ul class="product-more-infor mt-30">
                          <li>
                            <span>Category:</span> {{ product.category.title }}
                          </li>
                          <li>
                            <span>Price:</span> ${{ parseFloat(product.price).toFixed(2) }}
                          </li>
                        </ul>
                        <hr class="wp-block-separator is-style-dots" />
                        <p>
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit. (Additional description if available)
                        </p>
                        <h4 class="mt-30">Packaging & Delivery</h4>
                        <hr class="wp-block-separator is-style-wide" />
                        <p>Packaging & delivery details go here.</p>
                        <h4 class="mt-30">Suggested Use</h4>
                        <ul class="product-more-infor mt-30">
                          <li>Refrigeration not necessary.</li>
                          <li>Stir before serving</li>
                        </ul>
                        <h4 class="mt-30">Other Ingredients</h4>
                        <ul class="product-more-infor mt-30">
                          <li>Organic raw pecans, organic raw cashews.</li>
                          <li>This butter was produced using a LTG process</li>
                        </ul>
                        <h4 class="mt-30">Warnings</h4>
                        <ul class="product-more-infor mt-30">
                          <li>Oil separation occurs naturally. May contain pieces of shell.</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Recommended Products Section -->
              <div v-if="recommendedProducts.length" class="row mt-60">
                <div class="col-12">
                  <h2 class="section-title style-1 mb-30">Recommended products</h2>
                </div>
                <div class="col-12">
                  <div class="row related-products">
                    <div
                      v-for="(rec, index) in recommendedProducts"
                      :key="rec.id"
                      class="col-lg-3 col-md-4 col-12 col-sm-6"
                    >
                      <div class="product-cart-wrap hover-up">
                        <div class="product-img-action-wrap">
                          <div class="product-img product-img-zoom">
                            <router-link :to="`/product/${rec.id}`">
                              <img class="default-img" :src="rec.img_url" alt="" />
                            </router-link>
                          </div>
                          <div class="product-badges product-badges-position product-badges-mrg" v-if="rec.is_hot">
                            <span class="hot">Hot</span>
                          </div>
                        </div>
                        <div class="product-content-wrap">
                          <h2>
                            <router-link :to="`/product/${rec.id}`">
                              {{ rec.title }}
                            </router-link>
                          </h2>
                          <div class="rating-result" :title="rec.rating ? rec.rating * 20 + '%' : '0%'">
                            <span></span>
                          </div>
                          <div class="product-price">
                            <span>${{ parseFloat(rec.price).toFixed(2) }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Recommended Products Section -->
            </div>
          </div>
        </div>
      </div>
      <!-- Loading state -->
      <div v-else class="container">
        <p>Loading product details...</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch, nextTick } from 'vue';
  import { useRoute } from 'vue-router';
  import apiClient from '@/api/index.js';
  import emitter from '@/eventBus.js';
  
  const route = useRoute();
  const product = ref(null);
  const quantity = ref(1);
  const recommendedProducts = ref([]);
  const ratingWidth = '90%';
  const reviewCount = 32;
  
  const fetchProductDetails = async () => {
    try {
      const productId = route.params.id;
      const response = await apiClient.get(`/products/${productId}`);
      if (response.data.success) {
        product.value = response.data.data;
        await nextTick();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    } catch (error) {
      console.error('Error fetching product details:', error);
    }
  };
  
  const fetchRecommendedProducts = async () => {
    try {
      const productId = route.params.id;
      const response = await apiClient.get(`/recommend/similar/${productId}`);
      if (response.data.success) {
        recommendedProducts.value = response.data.recommendations || [];
      }
    } catch (error) {
      console.error('Error fetching recommended products:', error);
    }
  };
  
  onMounted(() => {
    fetchProductDetails();
    fetchRecommendedProducts();
  });
  
  watch(() => route.params.id, async (newId, oldId) => {
    if (newId !== oldId) {
      quantity.value = 1;
      await fetchProductDetails();
      await fetchRecommendedProducts();
    }
  });
  
  const incrementQuantity = () => {
    quantity.value++;
  };
  
  const decrementQuantity = () => {
    if (quantity.value > 1) {
      quantity.value--;
    }
  };
  
  const addToCart = async (prod) => {
    try {
      const response = await apiClient.post('/customer/cart', { product_id: prod.id, quantity: quantity.value });
      if (response.data.success) {
        alert('Product added to cart successfully');
      } else {
        alert('Failed to add product to cart');
      }
    } catch (error) {
      console.error('Error adding product to cart:', error);
      alert('Error adding product to cart');
    }
  };
  
  const toggleWishlist = async (prod) => {
    if (prod.in_wishlist) {
      // Remove from wishlist
      try {
        const response = await apiClient.delete(`/customer/wishlist/remove/${prod.id}`);
        if (response.data.success) {
          prod.in_wishlist = false;
          alert('Product removed from wishlist');
          emitter.emit('wishlistUpdated');
        } else {
          alert('Failed to remove product from wishlist');
        }
      } catch (error) {
        console.error('Error removing product from wishlist:', error);
        alert('Error removing product from wishlist');
      }
    } else {
      // Add to wishlist
      try {
        const response = await apiClient.post(`/customer/wishlist/add/${prod.id}`);
        if (response.data.success) {
          prod.in_wishlist = true;
          alert('Product added to wishlist successfully');
          emitter.emit('wishlistUpdated');
        } else {
          alert('Failed to add product to wishlist');
        }
      } catch (error) {
        console.error('Error adding product to wishlist:', error);
        alert('Error adding product to wishlist');
      }
    }
  };
  </script>
  
  
  <style scoped>
  /* Sort by buttons styling */
  .action-btn .fa-heart{
    color: #3bb77e;
  }
  .action-btn:hover .fa-heart{
    color:#fff;
  }
  .sort-by-buttons {
    display: flex;
    gap: 8px;
  }
  .sort-by-buttons button {
    background: #fff;
    border: 1px solid #eaeaea;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 14px;
  }
  .sort-by-buttons button.active {
    background: #3bb77e;
    color: #fff;
    border-color: #3bb77e;
  }
  
  /* Category list active styling */
  .sidebar-widget.widget-category-2 ul li.active {
    background: #f3f3f3;
  }
  .sidebar-widget.widget-category-2 ul li.active a {
    font-weight: bold;
    color: #3bb77e;
  }
  </style>
  