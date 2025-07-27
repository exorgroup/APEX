// resources/js/components/apex/core/widgets/DataTable/traits/useScroll.js

import { computed } from 'vue';

export function useScroll(props) {
    const scrollConfig = computed(() => ({
        scrollable: props.scrollable ?? false,
        scrollHeight: props.scrollHeight ?? 'flex',
        virtualScroll: props.virtualScroll ?? false
    }));

    return {
        scrollConfig
    };
}