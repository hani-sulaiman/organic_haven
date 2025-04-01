<template>
    <div class="container mb-30">
      <!-- Search Form -->
  
      <div class="row flex-row-reverse">
        <!-- Main Product Grid and Filter Controls -->
        <div class="col-lg-4-5">
          <!-- Filter Controls -->
          <div class="shop-product-fillter d-flex justify-content-between align-items-center mb-20">
            <div class="totall-product">
              <p>
                We found <strong class="text-brand">{{ products.total }}</strong> items for you!
              </p>
            </div>
            <div class="sort-by-product-area d-flex">
              <!-- Sort By Buttons -->
              <div class="sort-by-buttons">
                <button type="button"
                        @click="setSort('featured')"
                        :class="{ active: sortOption.value === 'featured' }">
                  Featured
                </button>
                <button type="button"
                        @click="setSort('price_asc')"
                        :class="{ active: sortOption.value === 'price_asc' }">
                  Price: Low to High
                </button>
                <button type="button"
                        @click="setSort('price_desc')"
                        :class="{ active: sortOption.value === 'price_desc' }">
                  Price: High to Low
                </button>
              </div>
            </div>
          </div>
          <!-- End Filter Controls -->
  
          <!-- Product Grid -->
          <div class="row product-grid">
            <div
              v-for="product in products.data"
              :key="product.id"
              class="col-lg-1-5 col-md-4 col-12 col-sm-6"
            >
              <div class="product-cart-wrap mb-30">
                <div class="product-img-action-wrap">
                  <div class="product-img product-img-zoom">
                    <router-link :to="`/product/${product.id}`">
                      <img class="default-img" :src="product.img_url" alt="" />
                      <!-- Optionally, a hover image -->
                      <img class="hover-img" :src="product.hover_img_url || product.img_url" alt="" />
                    </router-link>
                  </div>
                  <div class="product-badges product-badges-position product-badges-mrg" v-if="product.is_hot">
                    <span class="hot">Hot</span>
                  </div>
                </div>
                <div class="product-content-wrap">
                  <div class="product-category">
                    <router-link :to="`/category/${product.category_id}`">
                      {{ product.category?.title || 'Category' }}
                    </router-link>
                  </div>
                  <h2>
                    <router-link :to="`/product/${product.id}`">
                      {{ product.title }}
                    </router-link>
                  </h2>
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
            </div>
          </div>
          <!-- End Product Grid -->
  
          <!-- Pagination -->
          <div class="pagination-area mt-20 mb-20" v-if="products.last_page > 1">
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-start">
                <li class="page-item" :class="{ disabled: products.current_page === 1 }">
                  <a class="page-link" href="#" @click.prevent="changePage(products.current_page - 1)">
                    <i class="fi-rs-arrow-small-left"></i>
                  </a>
                </li>
                <li
                  v-for="page in pages"
                  :key="page"
                  class="page-item"
                  :class="{ active: products.current_page === page }"
                >
                  <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ disabled: products.current_page === products.last_page }">
                  <a class="page-link" href="#" @click.prevent="changePage(products.current_page + 1)">
                    <i class="fi-rs-arrow-small-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
    
        <!-- Sidebar: Categories List -->
        <div class="col-lg-1-5 primary-sidebar sticky-sidebar">
          <div class="sidebar-widget widget-category-2 mb-30">
            <h5 class="section-title style-1 mb-30">Category</h5>
            <ul>
              <li v-for="cat in allCategories" :key="cat.id" :class="{ active: selectedCategory === cat.id }">
                <a href="#" @click.prevent="filterByCategory(cat.id)">
                  {{ cat.title }}
                </a>
                <span class="count">{{ cat.products_count }}</span>
              </li>
            </ul>
          </div>
          <div class="banner-img wow fadeIn mb-lg-0 animated d-lg-block d-none">
            <img src="/assets/user/imgs/banner/banner-11.png" alt="" />
            <div class="banner-text">
              <span>Organic</span>
              <h4>
                Save 17% <br />
                on <span class="text-brand">Organic</span><br />
                Juice
              </h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
        
  <script setup>
  import { ref, onMounted, computed, watch } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import apiClient from '@/api/index.js';
  import emitter from '@/eventBus.js';
  
  const router = useRouter();
  const route = useRoute();
  
  // Filter state
  const query = ref('');
  const selectedCategory = ref(null);
  const sortBy = ref('title'); // default sort field
  const sortOrder = ref('asc'); // default sort order
  const unitTypes = ref([]);
  const perPage = ref(50);
  const currentPage = ref(1);
  
  // Products result (assumed paginated structure)
  const products = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
  });
  
  // All categories for sidebar
  const allCategories = ref([]);
  
  // Computed pages for pagination
  const pages = computed(() => {
    const arr = [];
    for (let i = 1; i <= products.value.last_page; i++) {
      arr.push(i);
    }
    return arr;
  });
  
  // Sort by options as buttons
  const sortOptions = {
    featured: { value: 'featured', label: 'Featured' },
    price_asc: { value: 'price_asc', label: 'Price: Low to High' },
    price_desc: { value: 'price_desc', label: 'Price: High to Low' },
  };
  const sortOption = ref(sortOptions.featured);
  
  const setSort = (option) => {
    sortOption.value = sortOptions[option];
    if (option === 'featured') {
      sortBy.value = 'title';
      sortOrder.value = 'asc';
    } else if (option === 'price_asc') {
      sortBy.value = 'price';
      sortOrder.value = 'asc';
    } else if (option === 'price_desc') {
      sortBy.value = 'price';
      sortOrder.value = 'desc';
    }
    currentPage.value = 1;
    fetchProducts();
  };
  
  const setPerPage = (value) => {
    perPage.value = value;
    currentPage.value = 1;
    fetchProducts();
  };
  
  const changePage = (page) => {
    if (page >= 1 && page <= products.value.last_page) {
      currentPage.value = page;
      fetchProducts();
    }
  };
  
  const fetchProducts = async () => {
    try {
      const params = {
        query: query.value,
        // Pass single category as array if selected, else empty array
        categories: selectedCategory.value ? [selectedCategory.value] : [],
        sort_by: sortBy.value,
        sort_order: sortOrder.value,
        unit_types: unitTypes.value,
        page: currentPage.value,
      };
      console.log(params);
      const response = await apiClient.get('/search', { params });
      if (response.data.success) {
        products.value = response.data.data;
      }
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  };
  
  const filterByCategory = (categoryId) => {
    if (selectedCategory.value === categoryId) {
      selectedCategory.value = null;
    } else {
      selectedCategory.value = categoryId;
    }
    currentPage.value = 1;
    fetchProducts();
    router.replace({
      query: { ...route.query, categories: selectedCategory.value ? selectedCategory.value : '' },
    });
  };
  
  const addToCart = async (product) => {
    try {
      const response = await apiClient.post('/customer/cart', { product_id: product.id, quantity: 1 });
      if (response.data.success) {
        emitter.emit('cartUpdated');
        alert('Product added to cart successfully');
      } else {
        alert('Failed to add product to cart');
      }
    } catch (error) {
      console.error('Error adding product to cart:', error);
      alert('Error adding product to cart');
    }
  };
  
  const addToWishlist = (product) => {
    console.log('Add to wishlist:', product);
  };
  
  const fetchCategories = async () => {
    try {
      const response = await apiClient.get('/categories');
      if (response.data.success) {
        allCategories.value = response.data.data || [];
      }
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };
  
  onMounted(() => {
    fetchCategories();
    if (route.query.categories) {
      selectedCategory.value = Number(route.query.categories);
    }
    if (route.query.q) {
      query.value = route.query.q;
    }
    fetchProducts();
  });
  
  watch(
  () => route.query.q, 
  (newQuery) => {
    if (newQuery) {
      query.value = newQuery;
    } else {
      query.value = '';
    }
    currentPage.value = 1;
    fetchProducts();
  }
);


  
  const handleSearch = () => {
    router.push({ path: '/products', query: { q: query.value } });
  };
  </script>
        
  <style scoped>
  /* Sort by buttons styling */
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
  
  /* Hide dropdown styling if present */
  .sort-by-dropdown {
    display: none;
  }
  
  /* Search form styling */
  .search-form {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
  }
  .search-input {
    flex: 1;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  .search-btn {
    padding: 8px 12px;
    border: none;
    background: #3bb77e;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
  }
  </style>
  