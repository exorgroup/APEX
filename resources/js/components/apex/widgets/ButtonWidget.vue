<script setup lang="ts">
import { computed } from 'vue';

// Declare global window functions
declare global {
    interface Window {
        [key: string]: any;
    }
} 

interface ButtonConfig {
    label: string;
    icon?: string;
    iconPos?: 'left' | 'right' | 'top' | 'bottom';
    severity?: 'primary' | 'secondary' | 'success' | 'info' | 'warning' | 'danger' | 'help' | 'contrast';
    size?: 'small' | 'normal' | 'large';
    outlined?: boolean;
    rounded?: boolean;
    text?: boolean;
    raised?: boolean;
    disabled?: boolean;
    loading?: boolean;
    loadingIcon?: string;
    badge?: string;
    badgeSeverity?: 'primary' | 'secondary' | 'success' | 'info' | 'warning' | 'danger' | 'contrast';
    onClick?: string;
    href?: string;
    target?: '_self' | '_blank' | '_parent' | '_top';
}

interface Props {
    widgetId: string;
    config: ButtonConfig;
}

const props = defineProps<Props>();

// Compute button classes based on size
const sizeClass = computed(() => {
    switch (props.config.size) {
        case 'small':
            return 'p-button-sm';
        case 'large':
            return 'p-button-lg';
        default:
            return '';
    }
});

// Handle click events
const handleClick = () => {
    if (props.config.onClick) {
        try {
            // Execute the onClick handler if it's a function name or inline code
            if (window[props.config.onClick] && typeof window[props.config.onClick] === 'function') {
                window[props.config.onClick]();
            } else {
                // Evaluate as inline JavaScript (use with caution)
                new Function(props.config.onClick)();
            }
        } catch (error) {
            console.error('Error executing button click handler:', error);
        }
    }
};

// Determine if we should render as a link or button
const isLink = computed(() => !!props.config.href);
</script>

<template>
    <div :id="widgetId" class="apex-button-widget">
        <!-- Link Button -->
        <a
            v-if="isLink"
            :href="config.href"
            :target="config.target"
            class="p-button p-component"
            :class="[
                sizeClass,
                {
                    'p-button-outlined': config.outlined,
                    'p-button-rounded': config.rounded,
                    'p-button-text': config.text,
                    'p-button-raised': config.raised,
                    'p-disabled': config.disabled
                }
            ]"
        >
            <PButton
                :label="config.label"
                :icon="config.icon"
                :iconPos="config.iconPos"
                :severity="config.severity"
                :outlined="config.outlined"
                :rounded="config.rounded"
                :text="config.text"
                :raised="config.raised"
                :disabled="config.disabled"
                :loading="config.loading"
                :loadingIcon="config.loadingIcon"
                :badge="config.badge"
                :badgeSeverity="config.badgeSeverity"
                :size="config.size"
                link
            />
        </a>
        
        <!-- Regular Button -->
        <PButton
            v-else
            :label="config.label"
            :icon="config.icon"
            :iconPos="config.iconPos"
            :severity="config.severity"
            :outlined="config.outlined"
            :rounded="config.rounded"
            :text="config.text"
            :raised="config.raised"
            :disabled="config.disabled"
            :loading="config.loading"
            :loadingIcon="config.loadingIcon"
            :badge="config.badge"
            :badgeSeverity="config.badgeSeverity"
            :size="config.size"
            @click="handleClick"
        />
    </div>
</template>

<style scoped>
.apex-button-widget {
    display: inline-block;
}

/* Ensure dark mode compatibility */
:deep(.p-button) {
    transition: all 0.2s ease-in-out;
}

/* Additional dark mode styles if needed */
.dark .apex-button-widget :deep(.p-button) {
    /* Dark mode specific styles will be handled by PrimeVue theme */
}
</style>