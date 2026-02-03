<template>
  <div class="quotes-container">
    <header class="header">
      <h1>📚 Quotes Package</h1>
      <p class="subtitle">A Laravel package with Vue.js + TypeScript</p>
    </header>

    <div class="content">
      <div class="actions">
        <h3>Test Actions</h3>
        <div class="buttons">
          <button @click="testApi" class="btn primary">
            Test API Connection
          </button>
          <button @click="showAlert" class="btn secondary">Show Alert</button>
        </div>
      </div>

      <div v-if="loading" class="loading">
        <p>Loading data...</p>
      </div>
    </div>

    <footer class="footer">
      <p>Built for Laravel Package Assessment</p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { Quote } from "../types/quotes";

const loading = ref<boolean>(false);
const quotes = ref<Quote[]>([]);

const testApi = async (): Promise<void> => {
  loading.value = true;
  try {
    // Esto se conectará a tu backend cuando esté listo
    const response = await fetch("/api/quotes");
    if (response.ok) {
      const data = await response.json();
      quotes.value = data.data || [];
      alert(`Fetched ${quotes.value.length} quotes successfully!`);
    } else {
      alert("API endpoint not ready yet. Implement QuoteController first.");
    }
  } catch (error) {
    alert("Error connecting to API. Check console for details.");
    console.error("API Error:", error);
  } finally {
    loading.value = false;
  }
};

const showAlert = (): void => {
  alert("Vue.js 3 + TypeScript is working correctly!");
};
</script>

<style scoped>
.quotes-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
    Ubuntu, sans-serif;
}

.header {
  text-align: center;
  margin-bottom: 40px;
  padding: 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.header h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
}

.subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

.content {
  display: grid;
  gap: 30px;
  margin-bottom: 40px;
}

.stats-card,
.actions {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.stats-card h3,
.actions h3 {
  color: #333;
  margin-bottom: 20px;
  font-size: 1.3rem;
}

.stats-card ul {
  list-style: none;
  padding: 0;
}

.stats-card li {
  padding: 8px 0;
  border-bottom: 1px solid #f0f0f0;
  color: #555;
}

.stats-card li:last-child {
  border-bottom: none;
}

.buttons {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn.primary {
  background: #4f46e5;
  color: white;
}

.btn.primary:hover {
  background: #4338ca;
  transform: translateY(-2px);
}

.btn.secondary {
  background: #f1f5f9;
  color: #475569;
}

.btn.secondary:hover {
  background: #e2e8f0;
  transform: translateY(-2px);
}

.loading {
  text-align: center;
  padding: 30px;
  color: #64748b;
}

.footer {
  text-align: center;
  padding: 20px;
  color: #94a3b8;
  font-size: 0.9rem;
  border-top: 1px solid #e2e8f0;
  margin-top: 40px;
}
</style>
