// resources/js/components/apex/core/widgets/DataTable/traits/useStateful.js

import { computed } from 'vue';

export function useStateful(props) {
    const statefulConfig = computed(() => ({
        stateStorage: props.stateStorage ?? null,
        stateKey: props.stateKey ?? null
    }));

    return {
        statefulConfig
    };
}