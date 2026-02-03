export interface Quote {
    id: number;
    quote: string;
    author: string;
}

export interface PaginatedQuotes {
    data: Quote[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
        last_page: number;
    };
}

export interface ApiError {
    message: string;
    code?: string;
}