// resources/js/components/apex/core/widgets/DataTable/traits/useTemplate.js

import { computed } from 'vue';

export function useTemplate(props) {
    const headerConfig = computed(() => props.header || null);
    
    const footerConfig = computed(() => 
        props.footer || { showRecordCount: true, showSelectedCount: true }
    );

    return {
        headerConfig,
        footerConfig
    };
}