<template>
    <div class="container mt-4">
      <h1>Categories</h1>
      <div class="filters mb-4">
        <button @click="toggleForm" class="btn btn-primary mr-2">Create New Category</button>
      </div>
      <div v-if="showForm" class="category-form mb-4">
        <h2>{{ editing ? 'Edit Category' : 'Create Category' }}</h2>
        <form @submit.prevent="saveCategory">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" v-model="category.title" id="title" class="form-control" required />
          </div>
          <button type="submit" class="btn btn-success mr-2">{{ editing ? 'Update Category' : 'Create Category' }}</button>
          <button type="button" @click="cancelEdit" class="btn btn-secondary">Cancel</button>
        </form>
      </div>
      <div class="row">
        <div v-for="category in categories" :key="category.id" class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ category.title }}</h5>
              <div class="btn-group mt-auto">
                <button @click="editCategory(category)" class="btn btn-primary btn-sm mr-2"><i class="fas fa-edit"></i> Edit</button>
                <button @click="deleteCategory(category.id)" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Delete</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import apiClient from '@/api/index';
  
  export default {
    data() {
      return {
        categories: [],
        showForm: false,
        editing: false,
        category: {
          id: null,
          title: '',
        },
      };
    },
    created() {
      this.fetchCategories();
    },
    methods: {
      async fetchCategories() {
        try {
          const response = await apiClient.get('/admin/categories');
          this.categories = response.data.data;
        } catch (error) {
          console.error('Error fetching categories:', error);
        }
      },
      toggleForm() {
        this.showForm = !this.showForm;
        this.resetForm();
      },
      editCategory(category) {
        this.editing = true;
        this.category = { ...category };
        this.showForm = true;
      },
      cancelEdit() {
        this.editing = false;
        this.resetForm();
        this.showForm = false;
      },
      resetForm() {
        this.category = {
          id: null,
          title: '',
        };
      },
      async saveCategory() {
        const formData = new FormData();
        formData.append('title', this.category.title);
  
        try {
          if (this.editing) {
            // Update existing category
            await apiClient.post(`/admin/categories/${this.category.id}`, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });
          } else {
            // Create new category
            await apiClient.post('/admin/categories', formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });
          }
          this.fetchCategories();
          this.toggleForm();
        } catch (error) {
          console.error('Error saving category:', error.response?.data || error.message);
        }
      },
      async deleteCategory(id) {
        try {
          await apiClient.delete(`/admin/categories/${id}`);
          this.fetchCategories();
        } catch (error) {
          console.error('Error deleting category:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 1200px;
  }
  
  .category-form {
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  
  .category-form h2 {
    margin-top: 0;
    font-size: 1.5em;
    color: #333;
  }
  
  .category-form .form-group label {
    font-weight: bold;
  }
  
  .card {
    height: 100%;
  }
  
  .card-body {
    display: flex;
    flex-direction: column;
  }
  
  .btn-group {
    margin-top: auto;
  }
  </style>