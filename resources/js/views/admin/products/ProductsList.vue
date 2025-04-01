<template>
    <div class="container mt-4">
      <h1>Products</h1>
      <div class="filters mb-4">
        <button @click="toggleForm" class="btn btn-primary mr-2">Create New Product</button>
        <select v-model="selectedCategory" @change="filterByCategory" class="form-control mr-2" style="width: auto;">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.title }}</option>
        </select>
        <input type="text" v-model="searchQuery" placeholder="Search..." @input="searchProducts" class="form-control" style="width: 200px;" />
      </div>
      <div v-if="showForm" class="product-form mb-4">
        <h2>{{ editing ? 'Edit Product' : 'Create Product' }}</h2>
        <form @submit.prevent="saveProduct">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" v-model="product.title" id="title" class="form-control" />
          </div>
          <div class="form-group">
            <label for="ingredients">Ingredients</label>
            <textarea v-model="product.ingredients" id="ingredients" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <label for="category_id">Category</label>
            <select v-model="product.category_id" id="category_id" class="form-control">
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.title }}</option>
            </select>
          </div>
          <div class="form-group">
            <label for="price">Price</label>
            <input type="number" step="0.01" v-model="product.price" id="price" class="form-control" />
          </div>
          <div class="form-group">
            <label for="unit_type">Unit Type</label>
            <select v-model="product.unit_type" id="unit_type" class="form-control">
              <option value="">Select Unit Type</option>
              <option value="lb">lb</option>
              <option value="oz">oz</option>
              <option value="ea">ea</option>
              <option value="ct">ct</option>
            </select>
          </div>
          <div class="form-group">
            <label for="unit_value">Unit Value</label>
            <input type="number" step="0.01" v-model="product.unit_value" id="unit_value" class="form-control" />
          </div>
          <div class="form-group">
            <label for="image">Image</label>
            <input type="file" @change="handleImageUpload" id="image" class="form-control-file" />
          </div>
          <button type="submit" class="btn btn-success mr-2">{{ editing ? 'Update Product' : 'Create Product' }}</button>
          <button type="button" @click="cancelEdit" class="btn btn-secondary">Cancel</button>
        </form>
      </div>
      <div class="row">
        <div v-for="(product, index) in paginatedProducts" :key="product.id" class="col-md-6 mb-4">
          <div class="card h-100">
            <img v-if="product.img_url" :src="product.img_url" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ product.title }}</h5>
              <p class="card-text">{{ truncateText(product.ingredients, 100) }}</p>
              <p class="card-text"><strong>Category:</strong> {{ product.category.title }}</p>
              <p class="card-text"><strong>Price:</strong> ${{ typeof product.price === 'number' ? product.price.toFixed(2) : 'N/A' }}</p>
              <p class="card-text"><strong>Unit Type:</strong> {{ product.unit_type }}</p>
              <p class="card-text"><strong>Unit Value:</strong> {{ product.unit_value }}</p>
              <div class="btn-group mt-auto">
                <button @click="editProduct(product)" class="btn btn-primary btn-sm mr-2"><i class="fas fa-edit"></i> Edit</button>
                <button @click="deleteProduct(product.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <a class="page-link" href="#" @click.prevent="prevPage">Previous</a>
          </li>
          <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
            <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <a class="page-link" href="#" @click.prevent="nextPage">Next</a>
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  
  export default {
    data() {
      return {
        products: [],
        categories: [],
        showForm: false,
        editing: false,
        product: {
          id: null,
          title: '',
          ingredients: '',
          category_id: '',
          price: null,
          unit_type: '',
          unit_value: null,
          img_url: null,
          imageFile: null, // To store the file object
        },
        selectedCategory: '',
        searchQuery: '',
        currentPage: 1,
        perPage: 10,
      };
    },
    computed: {
      filteredProducts() {
        return this.products.filter(product =>
          product.title.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      },
      totalPages() {
        return Math.ceil(this.filteredProducts.length / this.perPage);
      },
      paginatedProducts() {
        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;
        return this.filteredProducts.slice(start, end);
      },
    },
    created() {
      this.fetchProducts();
      this.fetchCategories();
    },
    methods: {
      async fetchProducts() {
        try {
          const response = await apiClient.get('/admin/products');
          // Ensure price is converted to a number
          this.products = response.data.data.map(product => ({
            ...product,
            price: parseFloat(product.price) || 0, // Convert price to float, default to 0 if invalid
          }));
        } catch (error) {
          console.error('Error fetching products:', error);
        }
      },
      async fetchCategories() {
        try {
          const response = await apiClient.get('/categories');
          this.categories = response.data.data;
        } catch (error) {
          console.error('Error fetching categories:', error);
        }
      },
      toggleForm() {
        this.showForm = !this.showForm;
        this.resetForm();
      },
      editProduct(product) {
        this.editing = true;
        this.product = { ...product, imageFile: null }; // Reset imageFile when editing
        this.showForm = true;
      },
      cancelEdit() {
        this.editing = false;
        this.resetForm();
        this.showForm = false;
      },
      resetForm() {
        this.product = {
          id: null,
          title: '',
          ingredients: '',
          category_id: '',
          price: null,
          unit_type: '',
          unit_value: null,
          img_url: null,
          imageFile: null, // Reset imageFile
        };
      },
      handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
          this.product.imageFile = file;
          this.product.img_url = URL.createObjectURL(file); // Preview the new image
        } else {
          this.product.imageFile = null;
          this.product.img_url = null; // Reset img_url if no file is selected
        }
      },
      async saveProduct() {
        const formData = new FormData();
        formData.append('title', this.product.title || '');
        formData.append('ingredients', this.product.ingredients || '');
        formData.append('category_id', this.product.category_id || '');
        formData.append('price', this.product.price || '');
        formData.append('unit_type', this.product.unit_type || '');
        formData.append('unit_value', this.product.unit_value || '');
  
        // Append image only if a new image is selected
        if (this.product.imageFile) {
          formData.append('image', this.product.imageFile);
        }
  
        try {
          if (this.editing) {
            // Update existing product
            await apiClient.post(`/admin/products/${this.product.id}`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });
          } else {
            // Create new product
            await apiClient.post('/admin/products', formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });
          }
          this.fetchProducts();
          this.toggleForm();
        } catch (error) {
          console.error('Error saving product:', error.response?.data || error.message);
        }
      },
      async deleteProduct(id) {
        try {
          await apiClient.delete(`/admin/products/${id}`);
          this.fetchProducts();
        } catch (error) {
          console.error('Error deleting product:', error);
        }
      },
      filterByCategory() {
        this.currentPage = 1;
        if (this.selectedCategory) {
          this.products = this.products.filter(product => product.category_id == this.selectedCategory);
        } else {
          this.fetchProducts();
        }
      },
      searchProducts() {
        this.currentPage = 1;
      },
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
        }
      },
      goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
          this.currentPage = page;
        }
      },
      truncateText(text, maxLength) {
        if (text.length > maxLength) {
          return text.substring(0, maxLength) + '...';
        }
        return text;
      },
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
  }
  
  .product-form {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .product-form h2 {
    margin-top: 0;
    font-size: 1.5em;
    color: #333;
  }
  
  .product-form .form-group label {
    font-weight: bold;
  }
  
  .pagination {
    margin-top: 20px;
  }
  
  .pagination .page-item.disabled .page-link {
    pointer-events: none;
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
  }
  
  .pagination .page-item.active .page-link {
    z-index: 1;
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
  }
  
  .card {
    height: 100%;
  }
  
  .card-body {
    display: flex;
    flex-direction: column;
  }
  
  .card-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Number of lines to show */
    -webkit-box-orient: vertical;
  }
  
  .btn-group {
    margin-top: auto;
  }
  </style>