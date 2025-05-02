import axios, {
    type AxiosInstance,
    AxiosError,
    type InternalAxiosRequestConfig,
    type RawAxiosRequestHeaders,
} from "axios";

export interface AuthStore {
    getAccessToken: string | null;
    setAccessToken?: (token: string) => void;
}

export interface ApiConfig {
    baseURL?: string;
    token?: string;
    onUnauthorized?: () => void;
    onServerError?: (error: any) => void;
    defaultHeaders?: Record<string, string>;
}

export interface ApiResponse<T> {
    status: "0" | "1";
    statusCode: number;
    message: string;
    errors?: Record<string, any>;
    response?: T;
}

export class ApiClient {
    private client: AxiosInstance;
    private config: ApiConfig;

    constructor(config: ApiConfig = {}) {
        this.config = {
            baseURL: window?.location?.origin,
            defaultHeaders: {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...(config.token
                    ? { Authorization: `Bearer ${config.token}` }
                    : {}),
            },
            ...config,
        };

        this.client = axios.create({
            baseURL: this.config.baseURL,
            headers: this.config.defaultHeaders,
            withCredentials: true,
        });

        this.setupInterceptors();
    }

    private setupInterceptors() {
        // Request interceptor
        this.client.interceptors.request.use(
            async (config: InternalAxiosRequestConfig) => {
                return config;
            },
            (error) => Promise.reject(error)
        );

        // Response interceptor
        this.client.interceptors.response.use(
            (response) => response.data,
            async (error: AxiosError) => {
                if (!error.config) {
                    return Promise.reject(error);
                }

                // Handle 401 Unauthorized
                if (
                    error.response?.status === 401 &&
                    !error.config.headers["Refresh-Token"]
                ) {
                    this.config.onUnauthorized?.();
                }

                // Handle 405 Method Not Allowed
                if (error.response?.status === 405) {
                    const onServerError =
                        this.config.onServerError?.(error.response?.data);
                }

                // Handle server errors
                if (error.response?.status === 500 || !error.response) {
                    const onServerError =
                        this.config.onServerError?.(error.response?.data);
                }

                return Promise.resolve(error.response?.data);
            }
        );
    }

    // API methods
    async request<T>(
        method: string,
        url: string,
        config: {
            params?: Record<string, any>;
            data?: Record<string, any>;
            headers?: RawAxiosRequestHeaders;
        } = {}
    ): Promise<ApiResponse<T>> {
        const { headers, params, data } = config;
        return this.client({ method, url, headers, params, data });
    }

    get<T>(
        url: string,
        params?: Record<string, any>,
        headers?: RawAxiosRequestHeaders
    ): Promise<ApiResponse<T>> {
        return this.request("GET", url, { params, headers });
    }

    post<T>(
        url: string,
        data?: Record<string, any>,
        headers?: RawAxiosRequestHeaders
    ): Promise<ApiResponse<T>> {
        return this.request("POST", url, { data, headers });
    }

    put<T>(
        url: string,
        data?: Record<string, any>,
        headers?: RawAxiosRequestHeaders
    ): Promise<ApiResponse<T>> {
        return this.request("PUT", url, { data, headers });
    }

    patch<T>(
        url: string,
        data?: Record<string, any>,
        headers?: RawAxiosRequestHeaders
    ): Promise<ApiResponse<T>> {
        return this.request("PATCH", url, { data, headers });
    }

    delete<T>(
        url: string,
        params?: Record<string, any>,
        headers?: RawAxiosRequestHeaders
    ): Promise<ApiResponse<T>> {
        return this.request("DELETE", url, { params, headers });
    }
}
