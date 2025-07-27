// resources/js/components/apex/pro/widgets/DataTable/traits/useSize.js

import { computed } from 'vue';

export function useSize(props) {
    const sizeConfig = computed(() => ({
        size: props.size ?? 'normal'
    }));

    const sizeClasses = computed(() => {
        const classes = [];
        if (sizeConfig.value.size === 'small') classes.push('p-datatable-sm');
        if (sizeConfig.value.size === 'large') classes.push('p-datatable-lg');
        return classes;
    });

    return {
        sizeConfig,
        sizeClasses
    };
}