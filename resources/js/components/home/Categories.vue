<template>
    <section class="popular-categories section-padding">
      <div class="container wow animate__animated animate__fadeIn">
        <div class="section-title">
          <div class="title">
            <h3>Featured Categories</h3>
          </div>
          <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow" id="carausel-10-columns-arrows"></div>
        </div>
        <div class="carausel-10-columns-cover position-relative">
          <div class="carausel-10-columns" id="carausel-10-columns">
            <div
              v-for="(category, index) in categories"
              :key="category.id"
              class="card-2 wow animate__animated animate__fadeInUp"
              :class="'bg-' + index.toString()"
              :data-wow-delay="getDelay(index)"
            >
              <h6>
                <router-link :to="`/products?categories=${category.id}`">
                  {{ category.title }}
                </router-link>
              </h6>
              <span>{{ category.products_count }} items</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import apiClient from '@/api/index.js';
  
  const categories = ref([]);
  
  const fetchCategories = async () => {
    try {
      const response = await apiClient.get('/categories');
      if (response.data.success) {
        categories.value = response.data.data || [];
      }
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };
  
  onMounted(() => {
    fetchCategories();
  });
  
  // A helper function to set an animation delay (optional)
  const getDelay = (index) => {
    // For example: .1s, .2s, etc.
    return `.${index + 1}s`;
  };
  </script>
  
  <style scoped>
  /* Add or adjust styles as needed */
  </style>
  