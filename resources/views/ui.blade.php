{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes Management - Laravel Package</title>

    <!-- Vue 3 desde CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        #app {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            background: #4f46e5;
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #4338ca;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .error {
            background: #fee;
            color: #c00;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .features {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="header">
            <h1>📚 Quotes Package</h1>
            <p>Laravel Package Assessment - Vue.js Interface</p>
        </div>

        <div class="content">
            <div class="loading" v-if="loading">
                <h3>Loading...</h3>
            </div>



            <div class="search-box">
                <input type="number" v-model="searchId" placeholder="Enter quote ID" @keyup.enter="searchQuote">
                <button @click="searchQuote">🔍 Search</button>
                <button @click="fetchQuotes">📥 Load Quotes</button>
            </div>

            <div v-if="quotes.length > 0">
                <h3>Quotes (@{{ quotes.length }} total)</h3>
                <div style="margin: 15px 0;">
                    <button @click="prevPage" :disabled="currentPage === 1">← Previous</button>
                    <span style="margin: 0 15px;">Page @{{ currentPage }} of @{{ totalPages }}</span>
                    <button @click="nextPage" :disabled="currentPage === totalPages">Next →</button>
                </div>

                <div style="display: grid; gap: 15px; margin-top: 20px;">
                    <div v-for="quote in paginatedQuotes" :key="quote.id"
                        style="border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px;">
                        <p style="font-style: italic;">"@{{ quote.quote }}"</p>
                        <p style="text-align: right; color: #4f46e5; margin-top: 10px;">— @{{ quote.author }}</p>
                        <p style="color: #888; font-size: 0.9rem;">ID: @{{ quote.id }}</p>
                    </div>
                </div>
            </div>

            <div v-else style="text-align: center; padding: 40px; color: #666;">
                <h3>No quotes loaded yet</h3>
                <p>Click "Load Quotes" to fetch data from API</p>
            </div>


        </div>
    </div>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    loading: false,
                    quotes: [],
                    searchId: '',
                    currentPage: 1,
                    perPage: 5,
                    errorMessage: ''
                };
            },

            computed: {
                totalPages() {
                    return Math.ceil(this.quotes.length / this.perPage);
                },
                paginatedQuotes() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.quotes.slice(start, start + this.perPage);
                }
            },

            methods: {
                async fetchQuotes() {
                    this.loading = true;
                    this.errorMessage = '';

                    try {
                        const response = await fetch('/quotes/api'); //?fetch_if_empty=true&auto_fetch_count=5
                        const data = await response.json();

                        if (data.success) {
                            console.log(data.data);
                            this.quotes = data.data;
                            this.errorMessage = '';

                            if (data.cache_info.was_empty) {
                                console.log('Cache was empty, auto-fetched initial quotes');
                            }

                            // Mostrar info de rate limit en consola
                            if (data.rate_limit) {
                                console.log('Rate limit:', data.rate_limit);
                            }
                        } else {
                            this.errorMessage = data.message || 'Failed to fetch quotes';
                        }
                    } catch (error) {
                        this.errorMessage = 'API error: ' + error.message;
                    } finally {
                        this.loading = false;
                    }
                },


                async searchQuote() {
                    if (!this.searchId) return;

                    this.loading = true;
                    try {
                        const response = await fetch(`/quotes/api/${this.searchId}`);
                        if (response.ok) {
                            const quote = await response.json();
                            alert(`Quote #${quote.id}:\n\n"${quote.quote}"\n\n- ${quote.author}`);
                        }
                    } catch (error) {
                        alert('Search failed');
                    } finally {
                        this.loading = false;
                        this.searchId = '';
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                }
            },

            mounted() {
                console.log('Quotes Package UI loaded');
            }
        }).mount('#app');
    </script>
</body>

</html> --}}




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes Management - Laravel Package</title>

    <!-- Vue 3 desde CDN -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        #app {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        button {
            padding: 10px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        button:hover:not(:disabled) {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        button:disabled {
            background: #c7d2fe;
            cursor: not-allowed;
            opacity: 0.7;
            transform: none;
            box-shadow: none;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #dc2626;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .warning {
            background: #fffbeb;
            color: #d97706;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #f59e0b;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .info {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .success {
            background: #f0fdf4;
            color: #16a34a;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #10b981;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .rate-limit-info {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .rate-limit-info h4 {
            margin-top: 0;
            color: #4f46e5;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .rate-limit-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #64748b;
            font-weight: 500;
        }

        .stat-value {
            font-weight: 600;
            color: #1e293b;
        }

        .remaining-high {
            color: #10b981;
        }

        .remaining-medium {
            color: #f59e0b;
        }

        .remaining-low {
            color: #ef4444;
        }

        .rate-limit-warning {
            background: #fff7ed;
            border: 2px solid #fed7aa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
        }

        .countdown {
            font-size: 2rem;
            font-weight: bold;
            color: #ea580c;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }

        .countdown-active {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }

            100% {
                opacity: 1;
            }
        }

        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .cache-info {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #bae6fd;
            padding: 15px 20px;
            border-radius: 10px;
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quote-item {
            border: 2px solid #e2e8f0;
            padding: 20px;
            border-radius: 10px;
            background: white;
            transition: all 0.3s;
        }

        .quote-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #c7d2fe;
        }

        .initial-loading {
            text-align: center;
            padding: 60px 40px;
            color: #4f46e5;
        }

        .initial-loading h3 {
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .btn-primary {
            background: #4f46e5;
        }

        .btn-secondary {
            background: #10b981;
        }

        .btn-danger {
            background: #ef4444;
        }

        .btn-warning {
            background: #f59e0b;
        }

        .pagination-controls {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            border: 2px solid #e2e8f0;
        }

        .pagination-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .pagination-nav {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .page-size-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-size-selector select {
            padding: 8px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            font-weight: 500;
            cursor: pointer;
        }

        .page-size-selector select:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #64748b;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px dashed #e2e8f0;
        }

        .empty-state h3 {
            margin-bottom: 15px;
            color: #475569;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            text-align: center;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .quote-grid {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 768px) {
            .quote-grid {
                grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            }
        }

        .quote-id {
            position: absolute;
            top: -10px;
            left: -10px;
            background: #4f46e5;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .quote-content {
            position: relative;
            padding: 10px;
        }

        .quote-text {
            font-style: italic;
            font-size: 1.2rem;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 15px;
        }

        .quote-author {
            text-align: right;
            color: #4f46e5;
            font-weight: 600;
            font-size: 1rem;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div id="app">
        <div class="header">
            <h1>📚 Quotes Management System</h1>
            <p>Laravel Package with Vue.js & Advanced Pagination</p>
        </div>

        <div class="content">
            <!-- Initial Loading State -->
            <div class="initial-loading" v-if="initialLoading">
                <div class="loading-spinner"></div>
                <h3>Loading All Quotes from Cache...</h3>
                <p>Please wait while we fetch all your saved quotes</p>
            </div>

            <!-- Main Content -->
            <div v-if="!initialLoading">
                <!-- Status Messages -->
                <div v-if="error" class="error">
                    <strong>⚠️ Error:</strong> @{{ error.message }}
                    <div v-if="error.retry_after" class="rate-limit-warning" style="margin-top: 15px;">
                        <p>⏳ Rate limit exceeded. You can retry in:</p>
                        <div class="countdown countdown-active">@{{ formatTime(error.retry_after) }}</div>
                        <p>Buttons will be automatically enabled when the countdown reaches zero.</p>
                    </div>
                    <div v-if="error.suggestion" style="margin-top: 10px;">
                        <strong>💡 Suggestion:</strong> @{{ error.suggestion }}
                    </div>
                </div>

                <div v-if="warning" class="warning">
                    <strong>⚠️ Warning:</strong> @{{ warning }}
                </div>

                <div v-if="success" class="success">
                    <strong>✅ Success:</strong> @{{ success }}
                </div>

                <div v-if="info" class="info">
                    <strong>ℹ️ Info:</strong> @{{ info }}
                </div>

                <!-- Cache Statistics -->
                <div v-if="cacheInfo && cacheInfo.total_cached > 0" class="cache-info">
                    <div>
                        <strong>📦 Cache Status:</strong>
                        <span style="font-size: 1.2rem; font-weight: bold; color: #4f46e5; margin-left: 5px;">
                            @{{ cacheInfo.total_cached }} quotes
                        </span>
                    </div>
                    <div style="font-size: 0.9rem; color: #64748b;">
                        Last updated: @{{ lastUpdated }}
                    </div>
                </div>

                <!-- Rate Limit Dashboard -->
                <div v-if="rateLimit" class="rate-limit-info">
                    <h4>📊 Rate Limit Dashboard</h4>
                    <div class="rate-limit-stats">
                        <div class="stat-item">
                            <span class="stat-label">Remaining Requests:</span>
                            <span class="stat-value" :class="getRemainingClass()">
                                @{{ rateLimit.remaining }} / @{{ rateLimit.limit }}
                            </span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Time Window:</span>
                            <span class="stat-value">@{{ rateLimit.window }} seconds</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Resets In:</span>
                            <span class="stat-value" v-if="rateLimit.remaining === 0 && rateLimit.reset_in > 0"
                                :class="{ 'countdown-active': rateLimit.reset_in > 0 }">
                                @{{ formatTime(rateLimit.reset_in) }}
                            </span>
                            <span class="stat-value" v-else>
                                Ready
                            </span>
                        </div>
                    </div>

                    <!-- Countdown when rate limited -->
                    <div v-if="rateLimit.remaining === 0 && rateLimit.reset_in > 0" class="rate-limit-warning"
                        style="margin-top: 15px;">
                        <p>🚫 Rate limit reached. You can make new requests in:</p>
                        <div class="countdown countdown-active">@{{ formatTime(rateLimit.reset_in) }}</div>
                        <p>All buttons will be automatically enabled when ready.</p>
                    </div>
                </div>

                <!-- Search & Controls -->
                <div class="controls">
                    <div class="search-box">
                        <input type="number" v-model="searchId" placeholder="Search quote by ID (1-100)"
                            @keyup.enter="searchQuote" :disabled="isRateLimited" min="1" max="100">
                        <button @click="searchQuote" :disabled="!searchId || isRateLimited">
                            🔍 Search
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button @click="fetchAllQuotes" :disabled="isRateLimited || loading" class="btn-primary">
                        📥 Load All Quotes
                    </button>
                    <button @click="fetchInitialQuotes" :disabled="isRateLimited || loading" v-if="!hasQuotes"
                        class="btn-secondary">
                        🚀 Fetch Initial Quotes
                    </button>
                    <button @click="refreshQuotes" :disabled="isRateLimited || loading" class="btn-warning">
                        🔄 Refresh Quotes
                    </button>
                    <button @click="clearCache" :disabled="isRateLimited || loading" class="btn-danger">
                        🗑️ Clear Cache
                    </button>
                </div>

                <!-- Loading State -->
                <div class="loading" v-if="loading">
                    <h3>Loading...</h3>
                </div>

                <!-- Main Content Area -->
                <div v-if="hasQuotes">
                    <!-- Statistics -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="number">@{{ quotes.length }}</div>
                            <div class="label">Total Quotes Loaded</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">@{{ totalPages }}</div>
                            <div class="label">Total Pages</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">@{{ perPage }}</div>
                            <div class="label">Quotes Per Page</div>
                        </div>
                        <div class="stat-card">
                            <div class="number">@{{ cacheInfo?.total_cached || 0 }}</div>
                            <div class="label">In Cache</div>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="pagination-controls">
                        <div class="pagination-top">
                            <div>
                                <h3 style="margin: 0; color: #4f46e5;">📖 Quotes Browser</h3>
                                <p style="margin: 5px 0 0 0; color: #64748b;">
                                    Showing @{{ paginatedQuotes.length }} of @{{ quotes.length }} quotes
                                    (Page @{{ currentPage }} of @{{ totalPages }})
                                </p>
                            </div>

                            <div class="page-size-selector">
                                <span style="color: #64748b; font-weight: 500;">Show:</span>
                                <select v-model="perPage" @change="currentPage = 1">
                                    <option value="5">5 per page</option>
                                    <option value="10">10 per page</option>
                                    <option value="20">20 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pagination Navigation -->
                        <div class="pagination-nav">
                            <button @click="goToFirstPage" :disabled="currentPage === 1 || isRateLimited">
                                ⏮️ First
                            </button>
                            <button @click="prevPage" :disabled="currentPage === 1 || isRateLimited">
                                ◀️ Previous
                            </button>

                            <div style="display: flex; gap: 5px;">
                                <button v-for="page in visiblePages" :key="page" @click="goToPage(page)"
                                    :disabled="isRateLimited"
                                    :style="{
                                        background: page === currentPage ? '#4f46e5' : '#e2e8f0',
                                        color: page === currentPage ? 'white' : '#475569'
                                    }">
                                    @{{ page }}
                                </button>
                                <span v-if="hasMorePages" style="align-self: center; color: #64748b;">...</span>
                            </div>

                            <button @click="nextPage" :disabled="currentPage === totalPages || isRateLimited">
                                Next ▶️
                            </button>
                            <button @click="goToLastPage" :disabled="currentPage === totalPages || isRateLimited">
                                Last ⏭️
                            </button>
                        </div>
                    </div>

                    <!-- Quotes Grid -->
                    <div class="quote-grid">
                        <div v-for="quote in paginatedQuotes" :key="quote.id" class="quote-item">
                            <div class="quote-content">
                                <div class="quote-id">@{{ quote.id }}</div>
                                <div class="quote-text">"@{{ quote.quote }}"</div>
                                <div class="quote-author">— @{{ quote.author }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Pagination -->
                    <div class="pagination-nav" style="margin-top: 30px;">
                        <button @click="prevPage" :disabled="currentPage === 1 || isRateLimited">
                            ◀️ Previous Page
                        </button>
                        <span style="padding: 0 20px; color: #64748b;">
                            Page @{{ currentPage }} of @{{ totalPages }}
                        </span>
                        <button @click="nextPage" :disabled="currentPage === totalPages || isRateLimited">
                            Next Page ▶️
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="!loading" class="empty-state">
                    <h3>📭 No Quotes Loaded</h3>
                    <p>Your cache is currently empty or no quotes have been loaded yet.</p>

                    <div style="margin-top: 30px;">
                        <button @click="fetchInitialQuotes" :disabled="isRateLimited"
                            style="padding: 15px 30px; font-size: 1.1rem;" class="btn-secondary">
                            🚀 Load Initial Quotes
                        </button>
                    </div>

                    <div
                        style="margin-top: 20px; font-size: 0.9rem; color: #94a3b8; max-width: 600px; margin-left: auto; margin-right: auto;">
                        <p>💡 <strong>Tip:</strong> The system will fetch quotes from the external API and cache them
                            automatically.</p>
                        <p>⏱️ <strong>Rate Limit:</strong> You can make @{{ rateLimit?.limit || 5 }} requests every
                            @{{ rateLimit?.window || 30 }} seconds.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    initialLoading: true,
                    loading: false,
                    quotes: [], // Todas las citas del caché
                    searchId: '',
                    currentPage: 1,
                    perPage: 10, // Valor por defecto - usuario puede cambiar
                    error: null,
                    warning: null,
                    success: null,
                    info: null,
                    cacheInfo: null,
                    rateLimit: null,
                    lastUpdated: 'just now',
                    countdownInterval: null,
                    maxVisiblePages: 5 // Para la paginación numérica
                };
            },

            computed: {
                // Paginación LOCAL en el frontend
                totalPages() {
                    return Math.ceil(this.quotes.length / this.perPage);
                },

                paginatedQuotes() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.quotes.slice(start, start + this.perPage);
                },

                isRateLimited() {
                    return this.rateLimit && this.rateLimit.remaining === 0;
                },

                hasQuotes() {
                    return this.quotes.length > 0;
                },

                // Cálculo para paginación numérica (1 2 3 ...)
                visiblePages() {
                    const pages = [];
                    const half = Math.floor(this.maxVisiblePages / 2);
                    let start = Math.max(1, this.currentPage - half);
                    let end = Math.min(this.totalPages, start + this.maxVisiblePages - 1);

                    // Ajustar si estamos cerca del final
                    if (end - start + 1 < this.maxVisiblePages) {
                        start = Math.max(1, end - this.maxVisiblePages + 1);
                    }

                    for (let i = start; i <= end; i++) {
                        pages.push(i);
                    }

                    return pages;
                },

                hasMorePages() {
                    return this.totalPages > this.maxVisiblePages &&
                        this.currentPage < this.totalPages - Math.floor(this.maxVisiblePages / 2);
                }
            },

            methods: {
                // Cargar TODAS las citas automáticamente al inicio
                async loadAllQuotesOnStart() {
                    console.log('🔄 Loading ALL quotes from cache...');

                    try {
                        this.loading = true;

                        // 1. Primero obtenemos estadísticas
                        const statsResponse = await fetch('/quotes/api/stats');
                        const statsData = await statsResponse.json();

                        if (statsData.success) {
                            const totalCached = statsData.data.cached_quotes_count || 0;
                            this.rateLimit = statsData.data.rate_limit;
                            this.cacheInfo = {
                                total_cached: totalCached
                            };

                            console.log(`📊 Cache has ${totalCached} quotes`);

                            if (totalCached > 0) {
                                // 2. Cargar TODAS las citas del endpoint principal
                                await this.fetchAllQuotes();
                            } else {
                                this.info = 'Cache is empty. Click "Fetch Initial Quotes" to load quotes.';
                            }

                            this.updateLastUpdated();
                            this.startCountdownIfNeeded();

                        } else {
                            console.error('Failed to load stats:', statsData);
                        }
                    } catch (error) {
                        console.error('Error in initial load:', error);
                        this.error = {
                            message: 'Failed to load initial data',
                            suggestion: 'Please refresh the page or check your connection'
                        };
                    } finally {
                        this.loading = false;
                        this.initialLoading = false;
                    }
                },

                // Cargar TODAS las citas del caché (una sola llamada)
                async fetchAllQuotes() {
                    this.clearMessages();
                    this.loading = true;

                    try {
                        // Ahora el backend devuelve TODAS las citas
                        const response = await fetch('/quotes/api');
                        const data = await response.json();

                        console.log('API Response:', data);

                        if (data.success) {
                            this.quotes = data.data || [];
                            this.rateLimit = data.rate_limit;
                            this.cacheInfo = {
                                total_cached: data.total_cached || this.quotes.length
                            };

                            this.success = `Loaded ${this.quotes.length} quotes from cache`;
                            this.updateLastUpdated();
                            this.startCountdownIfNeeded();

                            console.log(`✅ Successfully loaded ${this.quotes.length} quotes`);

                        } else {
                            this.handleApiError(data, response.status);
                        }
                    } catch (error) {
                        console.error('Error fetching all quotes:', error);
                        this.error = {
                            message: 'Failed to load quotes',
                            suggestion: 'Please try again'
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchInitialQuotes() {
                    this.clearMessages();
                    this.loading = true;

                    try {
                        const response = await fetch('/quotes/api');
                        const data = await response.json();

                        if (data.success) {
                            this.quotes = data.data || [];
                            this.rateLimit = data.rate_limit;
                            this.cacheInfo = {
                                total_cached: data.total_cached || this.quotes.length
                            };

                            this.success = `Loaded ${this.quotes.length} initial quotes`;
                            this.updateLastUpdated();
                            this.startCountdownIfNeeded();

                        } else {
                            this.handleApiError(data, response.status);
                        }
                    } catch (error) {
                        this.error = {
                            message: 'Failed to fetch initial quotes',
                            suggestion: 'Please try again'
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                async refreshQuotes() {
                    this.clearMessages();
                    this.loading = true;

                    try {
                        await this.fetchAllQuotes();
                        this.success = 'Quotes refreshed successfully';
                    } catch (error) {
                        this.error = {
                            message: 'Failed to refresh quotes',
                            suggestion: 'Please try again'
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                async searchQuote() {
                    if (!this.searchId) {
                        this.warning = 'Please enter a quote ID (1-100)';
                        return;
                    }

                    const id = parseInt(this.searchId);
                    if (id < 1 || id > 100) {
                        this.warning = 'Quote ID must be between 1 and 100';
                        return;
                    }

                    this.clearMessages();
                    this.loading = true;

                    try {
                        const response = await fetch(`/quotes/api/${id}`);
                        const data = await response.json();

                        if (response.ok && data.success) {
                            const quote = data.data;
                            this.success = `Found quote #${quote.id}`;
                            this.rateLimit = data.rate_limit || this.rateLimit;

                            // Agregar si no existe
                            const exists = this.quotes.some(q => q.id === quote.id);
                            if (!exists) {
                                this.quotes.unshift(quote); // Agregar al principio
                                this.info = `Quote #${quote.id} added to list`;
                                this.updateLastUpdated();
                            } else {
                                this.info = `Quote #${quote.id} already in list`;
                            }

                            this.currentPage = 1; // Ir a la primera página
                            this.startCountdownIfNeeded();

                        } else {
                            this.handleApiError(data, response.status);
                        }
                    } catch (error) {
                        this.error = {
                            message: 'Search failed',
                            suggestion: 'Try again with a different ID'
                        };
                    } finally {
                        this.loading = false;
                        this.searchId = '';
                    }
                },

                async clearCache() {
                    if (!confirm(
                            '⚠️ Are you sure you want to clear the cache?\n\nAll quotes will be removed and you will need to fetch them again.'
                            )) {
                        return;
                    }

                    this.clearMessages();
                    this.loading = true;

                    try {
                        const response = await fetch('/quotes/api/cache', {
                            method: 'DELETE'
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.success = 'Cache cleared successfully';
                            this.quotes = [];
                            this.rateLimit = data.data?.current?.rate_limit || null;
                            this.cacheInfo = {
                                total_cached: 0
                            };
                            this.info = `Previous cache had ${data.data?.previous_count || 0} quotes`;
                            this.updateLastUpdated();
                            this.startCountdownIfNeeded();
                        } else {
                            this.handleApiError(data, response.status);
                        }
                    } catch (error) {
                        this.error = {
                            message: 'Failed to clear cache',
                            suggestion: 'Please try again later'
                        };
                    } finally {
                        this.loading = false;
                    }
                },

                // Métodos de paginación LOCAL
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                    }
                },

                goToFirstPage() {
                    this.currentPage = 1;
                },

                goToLastPage() {
                    this.currentPage = this.totalPages;
                },

                handleApiError(data, status) {
                    console.log('API Error:', data, 'Status:', status);

                    this.stopCountdown();

                    switch (status) {
                        case 429:
                            const retryAfter = data.retry_after || 30;
                            this.error = {
                                message: data.message || 'Rate limit exceeded',
                                retry_after: retryAfter,
                                suggestion: 'Please wait for the countdown to finish'
                            };
                            this.rateLimit = {
                                remaining: 0,
                                limit: data.rate_limit?.limit || 5,
                                window: data.rate_limit?.window || 30,
                                reset_in: retryAfter
                            };
                            this.startCountdown();
                            break;
                        case 404:
                            this.error = {
                                message: data.message || 'Quote not found',
                                suggestion: 'Try a different ID between 1-100'
                            };
                            break;
                        case 422:
                            this.error = {
                                message: data.error || 'Validation failed',
                                suggestion: 'Check your input and try again'
                            };
                            break;
                        case 500:
                            this.error = {
                                message: data.message || 'Internal server error',
                                suggestion: 'Please try again later'
                            };
                            break;
                        default:
                            this.error = {
                                message: data.message || 'API error',
                                suggestion: 'Please try again'
                            };
                    }

                    if (data.rate_limit) {
                        this.rateLimit = data.rate_limit;
                        this.startCountdownIfNeeded();
                    }
                },

                startCountdown() {
                    this.stopCountdown();

                    if (this.rateLimit && this.rateLimit.reset_in > 0) {
                        this.countdownInterval = setInterval(() => {
                            if (this.rateLimit && this.rateLimit.reset_in > 0) {
                                this.rateLimit.reset_in--;

                                if (this.error && this.error.retry_after > 0) {
                                    this.error.retry_after--;
                                }

                                if (this.rateLimit.reset_in <= 0) {
                                    this.rateLimit.remaining = this.rateLimit.limit;
                                    this.rateLimit.reset_in = 0;
                                    this.stopCountdown();

                                    if (this.error && this.error.message.includes('Rate limit')) {
                                        this.error = null;
                                        this.info =
                                            '✅ Rate limit has been reset. You can make requests again.';
                                    }
                                }
                            } else {
                                this.stopCountdown();
                            }
                        }, 1000);
                    }
                },

                startCountdownIfNeeded() {
                    if (this.rateLimit && this.rateLimit.remaining === 0 && this.rateLimit.reset_in > 0) {
                        this.startCountdown();
                    }
                },

                stopCountdown() {
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                        this.countdownInterval = null;
                    }
                },

                clearMessages() {
                    this.error = null;
                    this.warning = null;
                    this.success = null;
                    this.info = null;
                },

                formatTime(seconds) {
                    if (!seconds || seconds <= 0) return '0s';
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
                },

                getRemainingClass() {
                    if (!this.rateLimit) return '';
                    const remaining = this.rateLimit.remaining;
                    const limit = this.rateLimit.limit;

                    if (remaining === 0) return 'remaining-low';
                    if (remaining <= Math.floor(limit * 0.3)) return 'remaining-low';
                    if (remaining <= Math.floor(limit * 0.6)) return 'remaining-medium';
                    return 'remaining-high';
                },

                updateLastUpdated() {
                    const now = new Date();
                    this.lastUpdated = now.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            },

            watch: {
                // Cuando cambia el tamaño de página, volver a la página 1
                perPage() {
                    this.currentPage = 1;
                },

                // Cuando se actualizan las citas, ajustar la página actual si es necesario
                quotes() {
                    if (this.currentPage > this.totalPages && this.totalPages > 0) {
                        this.currentPage = this.totalPages;
                    }
                }
            },

            mounted() {
                console.log('🎯 Quotes Management System loaded');
                console.log('🚀 Starting automatic load of ALL cached quotes...');

                // Cargar todas las citas automáticamente
                this.loadAllQuotesOnStart();

                // Configurar recarga con F5
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'F5') {
                        console.log('Manual refresh requested');
                    }
                });
            },

            beforeUnmount() {
                this.stopCountdown();
            }
        }).mount('#app');
    </script>
</body>

</html>
