// resources/js/components/apex/pro/widgets/DataTable/composables/usePreload.js

import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

export function usePreload(props) {
    const preloadedData = ref(null);
    const preloadLoading = ref(false);
    const preloadError = ref(null);

    const preloadConfig = computed(() => ({
        enabled: props.preload?.enabled ?? false,
        dataUrl: props.preload?.dataUrl ?? null,
        cacheKey: props.preload?.cacheKey ?? null,
        cacheDuration: props.preload?.cacheDuration ?? 300,
        backgroundRefresh: props.preload?.backgroundRefresh ?? false
    }));

    const getCacheKey = () => {
        return preloadConfig.value.cacheKey || `preload_${preloadConfig.value.dataUrl}`;
    };

    const isDataCached = () => {
        const cacheKey = getCacheKey();
        const cached = sessionStorage.getItem(cacheKey);
        if (!cached) return false;

        try {
            const { data, timestamp } = JSON.parse(cached);
            const now = Date.now();
            const maxAge = preloadConfig.value.cacheDuration * 1000;
            return (now - timestamp) < maxAge;
        } catch {
            return false;
        }
    };

    const getCachedData = () => {
        const cacheKey = getCacheKey();
        const cached = sessionStorage.getItem(cacheKey);
        if (!cached) return null;

        try {
            const { data } = JSON.parse(cached);
            return data;
        } catch {
            return null;
        }
    };

    const setCachedData = (data) => {
        const cacheKey = getCacheKey();
        const cacheItem = {
            data,
            timestamp: Date.now()
        };
        sessionStorage.setItem(cacheKey, JSON.stringify(cacheItem));
    };

    const preloadData = async () => {
        if (!preloadConfig.value.enabled || !preloadConfig.value.dataUrl) {
            return;
        }

        // Check cache first
        if (isDataCached()) {
            preloadedData.value = getCachedData();
            return;
        }

        preloadLoading.value = true;
        preloadError.value = null;

        try {
            const response = await axios.get(preloadConfig.value.dataUrl);
            const data = response.data;
            
            preloadedData.value = data;
            setCachedData(data);
        } catch (error) {
            preloadError.value = error.message;
            console.error('Preload error:', error);
        } finally {
            preloadLoading.value = false;
        }
    };

    const refreshData = async () => {
        // Clear cache and reload
        const cacheKey = getCacheKey();
        sessionStorage.removeItem(cacheKey);
        await preloadData();
    };

    // Auto-preload on mount if enabled
    onMounted(() => {
        if (preloadConfig.value.enabled) {
            preloadData();
        }
    });

    return {
        preloadConfig,
        preloadedData,
        preloadLoading,
        preloadError,
        preloadData,
        refreshData,
        isDataCached
    };
}